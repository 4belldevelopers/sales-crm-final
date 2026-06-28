@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

@endphp
@if (count($data) > 0)
    @foreach ($data as $row)
        <tr style="height: 102px; position: relative;">
            <td>
                @php
                   
                @endphp
            </td>
            <td class=" d-flex align-items-center">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            <td class="">
                @php
                   
                @endphp
            </td>
            
        </tr>
    @endforeach
@else
    <tr style="height: 102px; position: relative; background: #fffcfc7a;">

        <td colspan="9" style="text-align: center; color: black;">
            No data available in table
        </td>

    </tr>
@endif
