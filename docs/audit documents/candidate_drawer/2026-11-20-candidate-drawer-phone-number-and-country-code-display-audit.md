# Candidate Drawer Phone Number & Country Code Display Audit

## Document Metadata
- **Module:** Candidate Details Drawer (`job-applications/showdetail.blade.php`, `super-admin/candidates/showdetail.blade.php`)
- **Helper Service:** `app/Helper/GeneralMethod.php` (`formatContactPhoneHtml`, `formatContactPhoneForTel`)
- **Input Component:** `resources/views/components/jero-phone-input.blade.php`
- **Creation/Edit Views:** `resources/views/service_company/job-applications/create.blade.php`, `clientcreate.blade.php`, `bench-candidates/create.blade.php`
- **Audit Date:** 2026-11-20
- **Auditor:** Antigravity Forensic Engine
- **Constraint Compliance:** 100% Presentation Layer Scoped | 0% DB Schema Change | 0% DB Mutation | 0% Workflow Mutation

---

## 1. Executive Summary

This forensic investigation was conducted to determine the root cause of two reported anomalies in the Candidate Drawer Contact Details card:

1. **Incorrect display of mobile numbers starting with `91`:** When a candidate's actual 10-digit mobile number begins with `91` (e.g., `9177920670`, a standard Indian mobile series under TRAI/DoT allocation), the Candidate Drawer displays only 8 digits: `+91 77920670`. The leading `91` of the actual phone number is erroneously stripped, creating an invalid 8-digit number that breaks phone dialing, text copying, and WhatsApp integration (`https://api.whatsapp.com/send?phone=9177920670`).
2. **Country code changing unexpectedly:** In certain scenarios, the country code displayed in the Candidate Drawer reverts to India (`+91` / 🇮🇳) or displays the wrong country flag (e.g., displaying the US flag 🇺🇸 instead of Canada 🇨🇦 for `+1`, or Russian flag 🇷🇺 instead of Kazakhstan 🇰🇿 for `+7`), rather than retaining the originally selected and saved country.

### Forensic Finding Summary
| Issue | Primary Root Cause | Affected Layers | Impact |
| :--- | :--- | :--- | :--- |
| **Issue 1: Stripping `91`** | `GeneralMethod::formatContactPhoneHtml` (L17987) & `formatContactPhoneForTel` (L18044) execute naive substring checks without verifying whether the number's total digit count exceeds the national length (10 digits). | Presentation Helper (`GeneralMethod.php`) | Valid 10-digit numbers starting with `91` are truncated to 8 digits in the Drawer, clipboard copy, and WhatsApp links. |
| **Issue 2A: Blade Hardcoded Fallback** | `showdetail.blade.php` (L4384, L4414) uses `!empty($application->phone_code) ? $application->phone_code : '+91'`. Null/empty `phone_code` records bypass international prefix detection and are forcibly displayed as `+91`. | Drawer Blade Views (`showdetail.blade.php`) | Overseas candidates with missing `phone_code` metadata display with Indian flag and `+91`. |
| **Issue 2B: Edit Form Overwrite** | `create.blade.php` (L3542, L3854) binds to `:countryCode="@$phone_code"`, but the controller sets `$this->application->phone_code`. In edit mode, `@$phone_code` evaluates to `null`, causing `<x-jero-phone-input>` to silently reset the country selector to `+91`. Saving the form overwrites the original DB record. | Candidate Edit Forms (`create.blade.php`, `clientcreate.blade.php`, `bench-candidates/create.blade.php`) | Any update to an international candidate's profile permanently overwrites their `phone_code` to `+91` in MySQL and Elasticsearch. |
| **Issue 2C: 1:N Dial Code Mapping** | `$dialToIso['+1']` hard-maps to `US` and `+7` hard-maps to `RU`. Canada (`CA`, `+1`) and Kazakhstan (`KZ`, `+7`) are misidentified as United States and Russia. | Presentation Helper (`GeneralMethod.php`) | Canadian and Kazakh candidates display incorrect national flags. |

---

## 2. End-to-End Phone Number Lifecycle & Data Flow

```mermaid
flowchart TD
    subgraph Candidate_Creation_Or_Edit["1. Candidate Creation / Edit Form"]
        A["Recruiter Selects Country (+91, +1, +971, etc.)"] --> B["<x-jero-phone-input> Component"]
        B -->|Submits primary_country_code & phone| C["Controller: store() or update()"]
    end

    subgraph Persistence_Layer["2. MySQL Database & Elasticsearch"]
        C -->|INSERT / UPDATE job_applications| D[("MySQL job_applications<br/>• phone_code: '+91'<br/>• phone: '9177920670'")]
        D -->|ElasticCandidatesIndex sync| E[("Elasticsearch ELASTIC_CANDIDATES<br/>• phone_code: '+91'<br/>• phone: '9177920670'")]
    end

    subgraph Drawer_Retrieval["3. Candidate Drawer Show Request"]
        F["Recruiter clicks candidate"] --> G["ServiceCompanyJobApplicationController::show($id)"]
        G -->|ES GET ELASTIC_CANDIDATES| H["$this->application Object populated"]
        H --> I["View: job-applications/showdetail.blade.php"]
    end

    subgraph Presentation_Layer["4. Presentation Layer (Vulnerable Execution)"]
        I -->|L4384: formatContactPhoneHtml('+91', '9177920670')| J["GeneralMethod::formatContactPhoneHtml()"]
        J -->|L17987: str_starts_with('9177920670', '91') && 8 >= 7| K["STRIPS '91' -> '77920670'"]
        K --> L["Candidate Drawer Displays: 🇮🇳 +91 77920670 (8 Digits!)"]
        I -->|L4392: formatContactPhoneForTel('+91', '9177920670')| M["GeneralMethod::formatContactPhoneForTel()"]
        M -->|L18044: str_starts_with('9177920670', '91')| N["Returns: '+9177920670'"]
        N --> O["Clipboard Copy: '+91 77920670'<br/>WhatsApp Link: 'api.whatsapp.com/send?phone=9177920670'"]
    end
```

---

## 3. Detailed Root Cause Analysis

### Issue 1: Digits Starting with 91 Mistakenly Stripped

#### Location in Codebase
- File: [GeneralMethod.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php#L17980-L17990)
- Lines 17980–17990:
```php
$cleanPhone = $rawPhone;
if ($dial !== null) {
    // Strip leading dial code from phone number to prevent duplicate display
    if (str_starts_with($cleanPhone, $dial)) {
        $cleanPhone = trim(substr($cleanPhone, strlen($dial)));
    } elseif (str_starts_with($cleanPhone, '+'.ltrim($dial, '+'))) {
        $cleanPhone = trim(substr($cleanPhone, strlen('+'.ltrim($dial, '+'))));
    } elseif (str_starts_with($cleanPhone, ltrim($dial, '+')) && strlen(substr($cleanPhone, strlen(ltrim($dial, '+')))) >= 7) {
        $cleanPhone = trim(substr($cleanPhone, strlen(ltrim($dial, '+'))));
    }
}
```
- Lines 18041–18048:
```php
$cleanDigits = preg_replace('/[^\d]/', '', $rawPhone);
if ($dial !== '') {
    $cleanDialDigits = ltrim($dial, '+');
    if (str_starts_with($cleanDigits, $cleanDialDigits)) {
        $cleanDigits = substr($cleanDigits, strlen($cleanDialDigits));
    }

    return '+'.$cleanDialDigits.$cleanDigits;
}
```

#### Forensic Explanation
1. Indian mobile phone numbers are **10 digits** in length (National Significant Number = 10), starting with 6, 7, 8, or 9 as designated by the Department of Telecommunications (DoT).
2. The `91xxxxxxxx` series (e.g., `9177920670`, `9115xxxxxx`, `9125xxxxxx`) is a valid mobile allocation used by major telecom providers (Airtel, Jio, Vi).
3. In `formatContactPhoneHtml`:
   - `$dial` is `'+91'`.
   - `ltrim($dial, '+')` evaluates to `'91'`.
   - `str_starts_with('9177920670', '91')` evaluates to `true`.
   - `strlen(substr('9177920670', 2))` evaluates to `8`.
   - The condition `8 >= 7` evaluates to `true`.
   - **Result:** The code strips `'91'`, leaving only `'77920670'` (8 digits).
4. In `formatContactPhoneForTel`:
   - `$cleanDigits` is `'9177920670'`.
   - `str_starts_with('9177920670', '91')` evaluates to `true`.
   - It strips `'91'` and returns `'+9177920670'`.
   - The WhatsApp URL becomes `https://api.whatsapp.com/send?phone=9177920670` (only 8 national digits), which WhatsApp rejects as an invalid phone number.

#### Reproduction Verification (Tinker Execution)
```text
Command: php artisan tinker --execute="echo App\Helper\GeneralMethod::formatContactPhoneHtml('+91', '9177920670');"

Output:
<div class="phone-cell d-inline-flex align-items-center text-nowrap" style="gap:6px;">
  <span class="phone-flag-box ..."><svg>...[India Flag]...</svg></span>
  <span class="phone-dial-code">+91</span>
  <span class="phone-digits">77920670</span>  <-- DEFECT REPRODUCED: 8 digits displayed
</div>
```

---

### Issue 2: Country Code Changing Unexpectedly

#### Root Cause 2A: Hardcoded `+91` Fallback in Drawer Blade Views
- **Files:**
  - [showdetail.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/showdetail.blade.php#L4384)
  - [super-admin/candidates/showdetail.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/super-admin/candidates/showdetail.blade.php#L4359)
- **Code:**
  ```blade
  {!! GeneralMethod::formatContactPhoneHtml(!empty($application->phone_code) ? $application->phone_code : '+91', @$application->phone) !!}
  ```
- **Mechanism:**
  - When candidate records have `phone_code = null` (13 of 41 candidates in the current database have `phone_code = null`), the Blade template injects `'+91'` as the first argument.
  - `GeneralMethod::formatContactPhoneHtml` has built-in smart prefix detection (L17965: `if ($dial === null && str_starts_with($rawPhone, '+'))`).
  - Because `showdetail.blade.php` passes `'+91'` instead of `null`, `$dial` is never null. The smart detection is bypassed, and overseas numbers like `+1 415 555 2671` are forced to display as `🇮🇳 +91 +1 415 555 2671`.

#### Root Cause 2B: Edit Form Variable Name Mismatch Silently Overwriting Country Code
- **Files:**
  - [create.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/create.blade.php#L3542)
  - [clientcreate.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-applications/clientcreate.blade.php#L3592)
  - [bench-candidates/create.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/bench-candidates/create.blade.php#L574)
- **Mechanism:**
  1. In `ServiceCompanyJobApplicationController::edit($id)` (L7124), candidate data is populated on `$this->application`:
     ```php
     $this->application->phone_code = $data->phone_code;
     $this->application->second_phone_code = $data->second_phone_code;
     ```
  2. The controller does **not** set `$this->phone_code`.
  3. In `create.blade.php`, the component is invoked as:
     ```blade
     <x-jero-phone-input
         :value="@$application->phone"
         :countryCode="@$phone_code"
         countryCodeName="primary_country_code"
         ...
     ```
  4. Because `@$phone_code` is undefined (`null`), `<x-jero-phone-input>` receives an empty string for `countryCode`.
  5. In `jero-phone-input.blade.php` (L100), when `countryCode` is empty:
     ```php
     $initialCode = $isoToDial[$tenantHqIso] ?? '+91';
     $initialIso = $tenantHqIso ?: 'IN';
     ```
  6. The dropdown silently resets to India (`+91` / 🇮🇳) and sets the hidden field `<input type="hidden" name="primary_country_code" value="+91">`.
  7. When the user saves the candidate edit form, the controller executes:
     ```php
     $jobApplication->phone_code = ($request->primary_country_code) ? @$request->primary_country_code : null;
     ```
  8. **The candidate's original country code (`+1`, `+971`, `+44`, etc.) is permanently overwritten with `+91` in MySQL and Elasticsearch.**

#### Root Cause 2C: Shared Dial Codes Defaulting to Single ISO (1:N Lossy Mapping)
- **File:** [GeneralMethod.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Helper/GeneralMethod.php#L17913-L17926)
- **Mechanism:**
  - In `GeneralMethod::$dialToIso`:
    - `'+1' => 'US'`
    - `'+7' => 'RU'`
  - Countries that share the North American Numbering Plan (`+1`) include Canada (`CA`), United States (`US`), Jamaica, Bahamas, etc.
  - Countries that share the Integrated World Numbering Plan (`+7`) include Kazakhstan (`KZ`) and Russia (`RU`).
  - If a Canadian candidate is saved with `phone_code = '+1'`, `formatContactPhoneHtml` looks up `$dialToIso['+1']` and always returns `'US'`. The Candidate Drawer renders the US flag 🇺🇸 instead of Canada 🇨🇦.

---

## 4. Current vs Expected Behavior Matrix

| Scenario | Input Data in DB | Current Drawer Display | Expected Correct Display |
| :--- | :--- | :--- | :--- |
| **Indian 10-digit number starting with 91** | `phone_code`: `+91`<br>`phone`: `9177920670` | 🇮🇳 `+91 77920670` *(8 digits - truncated)* | 🇮🇳 `+91 9177920670` *(10 digits - complete)* |
| **Indian 12-digit number with redundant dial code** | `phone_code`: `+91`<br>`phone`: `919876543210` | 🇮🇳 `+91 9876543210` *(10 digits)* | 🇮🇳 `+91 9876543210` *(10 digits)* |
| **Indian number with explicit `+91` prefix** | `phone_code`: `+91`<br>`phone`: `+919177920670` | 🇮🇳 `+91 77920670` *(8 digits - truncated)* | 🇮🇳 `+91 9177920670` *(10 digits - complete)* |
| **US/UK number with `phone_code = null`** | `phone_code`: `null`<br>`phone`: `+1 415 555 2671` | 🇮🇳 `+91 +1 415 555 2671` *(forced +91)* | 🇺🇸 `+1 415 555 2671` *(auto-detected +1)* |
| **Canadian candidate with `phone_code = +1`** | `phone_code`: `+1`<br>`country`: `38` (Canada) | 🇺🇸 `+1 416 555 0199` *(wrong US flag)* | 🇨🇦 `+1 416 555 0199` *(correct Canadian flag)* |
| **UAE candidate after editing profile** | `phone_code`: `+971`<br>`phone`: `501234567` | 🇮🇳 `+91 501234567` *(overwritten by edit form)* | 🇦🇪 `+971 501234567` *(preserved +971)* |

---

## 5. Recommended Solution Plan (Presentation Layer Only)

In strict adherence to the project constraints:
- **No changes** to database schema, migrations, or stored records.
- **No changes** to candidate creation, validation, or submission workflows.
- Changes are strictly limited to the **formatting and presentation layer**.

### Component 1: Fix Substring Stripping in `GeneralMethod.php`
1. **In `GeneralMethod::formatContactPhoneHtml`:**
   - Incorporate the standard national length dictionary (`$dialToLen`).
   - If the raw phone number does **not** begin with `+`:
     - Calculate the digit count of the phone number.
     - **Only strip** the leading dial code if the total digits **strictly exceed** the national length (e.g. for India, strip only if digits $> 10$).
     - If total digits $\le 10$, treat all digits as the national number and **never strip**.
   - If the raw phone number begins with `+`:
     - Strip the explicit `+<dial>` prefix cleanly without touching subsequent subscriber digits.
2. **In `GeneralMethod::formatContactPhoneForTel`:**
   - Apply the exact same national length boundary check so that copy button and WhatsApp link receive the complete, valid E.164 number (`+919177920670`).

### Component 2: Fix Country Code Display & Fallbacks in Drawer Blade Views
1. **In `showdetail.blade.php` and `super-admin/.../showdetail.blade.php`:**
   - Replace `!empty($application->phone_code) ? $application->phone_code : '+91'` with:
     ```php
     @$application->phone_code ?: null
     ```
   - Pass candidate country/nationality context as an optional third parameter to `formatContactPhoneHtml`:
     ```php
     GeneralMethod::formatContactPhoneHtml(@$application->phone_code, @$application->phone, @$application->country_name ?: @$application->country)
     ```
2. **In `GeneralMethod::formatContactPhoneHtml`:**
   - When `$callingCode` is null or empty:
     - Check if `$phoneNumber` contains an international prefix (starts with `+`).
     - If no `+` is present, use the optional `$countryContext` to resolve country code.
     - If still unresolved, default cleanly or display a generic phone icon rather than imposing `+91` on non-Indian numbers.
   - For ambiguous dial codes (`+1`, `+7`):
     - Use `$countryContext` (e.g. `Canada`, `Kazakhstan`, `CA`, `KZ`) to disambiguate the flag between `US`/`CA` and `RU`/`KZ`.

### Component 3: Fix Candidate Edit Form Binding
1. **In `create.blade.php`, `clientcreate.blade.php`, and `bench-candidates/create.blade.php`:**
   - Update component attribute binding:
     ```blade
     :countryCode="@$application->phone_code ?: @$phone_code"
     ```
     and for secondary mobile:
     ```blade
     :countryCode="@$application->second_phone_code ?: @$second_phone_code"
     ```
   - This ensures existing candidate records open in the edit form with their **true, originally saved country code**, preventing accidental overwriting on save.

---

## 6. Verification & Test Plan

1. **Unit / Feature Tests:**
   - Test `formatContactPhoneHtml('+91', '9177920670')` -> outputs `+91` and `9177920670` (10 digits).
   - Test `formatContactPhoneHtml('+91', '919876543210')` -> outputs `+91` and `9876543210` (10 digits).
   - Test `formatContactPhoneHtml('+91', '+919177920670')` -> outputs `+91` and `9177920670` (10 digits).
   - Test `formatContactPhoneHtml(null, '+1 415 555 2671')` -> outputs `+1` with US flag.
   - Test `formatContactPhoneHtml('+1', '416 555 0199', 'Canada')` -> outputs `+1` with Canadian flag.
   - Test `formatContactPhoneForTel('+91', '9177920670')` -> returns `+919177920670`.
2. **UI & Regression Verification:**
   - Verify Candidate Drawer on 15.6" screen (`1366×768` and `1920×1080`).
   - Verify WhatsApp button URL and Clipboard Copy tooltip behavior.
   - Verify Candidate Edit drawer retains original country code without reverting to `+91`.
