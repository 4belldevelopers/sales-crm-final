<form action="{{ route('parseResume') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="cv">Upload CV:</label>
    <input type="file" name="cv" id="cv" required>
    <button type="submit">Upload and Parse</button>
</form>
