<?php  
 use App\Helper\GeneralMethod;
$company_name = GeneralMethod::CompantName();
    ?>
    <p style="padding-bottom:.5em;margin:0;padding:2px 0;">Best Regards,<br>
        <?= $company_name->company_name ?><br>
        Recruitment Department
    </p>