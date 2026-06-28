<ul class="list-unstyled ">
  <?php

	$cnt=0;
    foreach ($notes as $note)
	{
		$photo	=	$noteTitme	=	$noteDate	= '';
		
		$date						=  $note->created_at;
		$noteDate 					=  date("d-M-Y", strtotime($date));
		$noteTitme 					=  date("h:i A", strtotime($date));
		
		$posted_by_image			=	'';
		
		$posted_by_image			=  '';
		if(!empty($note->user->image)){	
			$posted_by_image		=  url('public/user-uploads/team_profile_img/'.@$note->user->image);
		}
		
		$photo						=  'https://preview.keenthemes.com/metronic8/demo1/assets/media/avatars/blank.png';	

		if(!empty($note->job_application->photo)){
			$photo					=  url('/public/user-uploads/candidate-photos/'.$note->job_application->photo);	
		}

		//job_application					
?>
  <li class="media timeline-items" id="note-{{ $note->id }}" >
    <div class="timeline-item">
      <?php 
	if($cnt%3==0){
?>
      <div class="timeline-media"> <span class="svg-icon svg-icon-danger svg-icon-2x">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"></rect>
            <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3"></path>
            <path d="M11.1750002,14.75 C10.9354169,14.75 10.6958335,14.6541667 10.5041669,14.4625 L8.58750019,12.5458333 C8.20416686,12.1625 8.20416686,11.5875 8.58750019,11.2041667 C8.97083352,10.8208333 9.59375019,10.8208333 9.92916686,11.2041667 L11.1750002,12.45 L14.3375002,9.2875 C14.7208335,8.90416667 15.2958335,8.90416667 15.6791669,9.2875 C16.0625002,9.67083333 16.0625002,10.2458333 15.6791669,10.6291667 L11.8458335,14.4625 C11.6541669,14.6541667 11.4145835,14.75 11.1750002,14.75 Z" fill="#000000"></path>
          </g>
        </svg>
        </span> </div>
      <?php 
}else if($cnt%3==1){
?>
      <div class="timeline-media"> <span class="svg-icon svg-icon-warning svg-icon-2x">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <polygon points="0 0 24 0 24 24 0 24"></polygon>
            <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
            <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
          </g>
        </svg>
        </span> </div>
      <?php 
}else if($cnt%3==2){
?>
      <div class="timeline-media"> <span class="svg-icon svg-icon-primary svg-icon-2x">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"></path>
            <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"></rect>
          </g>
        </svg>
        </span> </div>
      <?php 
}
?>
      <div class="timeline-content">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="me-2 fs-6"> <a href="javascript:void(0);" class="text-dark text-hover-primary fw-bolder me-2"><span class="text-muted me-2"><?php echo @$noteTitme; ?> | <?php echo @$noteDate; ?></a> </span>
            <div class="symbol symbol-circle symbol-25px" data-toggle="tooltip" data-placement="top" title="{{ ucwords($note->user->name) }}"> <img src="<?php echo @$posted_by_image; ?>" alt="img"> </div>
          </div>
          <div class="dropdown ml-2" data-toggle="tooltip" title="" data-placement="left" data-original-title="New Project"> <a href="javascript:void(0);" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <a href="javascript:;" class="edit-note" data-note-id="{{ $note->id }}"><i class="fa fa-edit ml-2"></i> </a> <a href="javascript:;" class="delete-note" data-note-id="{{ $note->id }}"><i class="fa fa-trash ml-1 text-danger"></i></a> </div>
        </div>
        <p class="p-0 fs-6 comment-para note-text">{{ ucfirst($note->note_text) }}</p>
        <div class="note-textarea"></div>
      </div>
    </div>
  </li>
  <?php
$cnt++; 
	}
?>
</ul>
