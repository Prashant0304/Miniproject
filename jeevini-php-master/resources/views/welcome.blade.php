


@foreach($imageData as $data)
        
         {{Storage::disk('s3')->url('PatientProfile/' . $data->profile)}}
	  
        @endforeach     
 