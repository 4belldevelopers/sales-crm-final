<?php

namespace App\Exports;

use App\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

use Illuminate\Support\Facades\DB;

class PostsExport implements FromCollection
{
   
	
	protected $id;

	 function __construct($id) {
			$this->id = $id;
	 }


   public function collection()
    {
		return $SQLSOLD = DB::table('posts')->where('id', '=', $this->id)->get();
       // return Post::all();
    }
	
	
	
	
}
