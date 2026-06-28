<!DOCTYPE html>
<html>
<head>
    <title>Resume Upload & Search</title>
</head>
<body>

<h2>Upload Resume</h2>
<form action="{{ route('resume.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="resume" required>
    <button type="submit">Upload</button>
</form>

<hr>

<h2>Search Resumes</h2>
<form action="{{ route('resume.search') }}" method="GET">
    <input type="text" name="query" placeholder="Search keywords..." required>
    <input type="text" name="location" placeholder="Location">
    <button type="submit">Search</button>
</form>

@if(isset($results))
    <h3>Results</h3>
    @foreach($results as $res)
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <strong>{{ $res['_source']['filename'] }}</strong><br>
            <pre>{{ Str::limit($res['_source']['text'], 500) }}</pre>
        </div>
    @endforeach
@endif

</body>
</html>
