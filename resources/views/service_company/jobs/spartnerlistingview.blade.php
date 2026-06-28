

@if($records)

@foreach ($records as $item)
    

   <tr class="fw-bolder text-muted" style="border-bottom-width: 0px;">
            {{-- <td class="w-10px"> {!!  $item['chkbox'] !!}</td> --}}
            <td class="min-w-150px">{!!  $item['Name'] !!}</td>
            <td class="min-w-50px">{!!  $item['SimilarReq'] !!}</td>
            <td class="min-w-50px">{!!  $item['Profiles'] !!}</td>
            <td class="min-w-50px">{!!  $item['Location'] !!}</td>
            <td class="min-w-100px">{!!  $item['HiringType'] !!}</td>
            <td class="min-w-50px">{!!  $item['Status'] !!}</td>
            <td class="min-w-100px">{!!  $item['Action'] !!}</td>
    </tr>
    <tr class="fw-bolder text-muted">
        <td class="w-10px"> {!!  $item['Skills'] !!}</td>
    </tr>
  @endforeach
  @endif