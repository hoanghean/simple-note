<?php
$notes = glob('notes/*.txt');
if (isset($_GET['note'])) {
    $note_name = $_GET['note'];
    $note_file = "notes/{$note_name}.txt";
    $note_data = file_get_contents($note_file);
    list($note_title, $note_content) = explode(' || ', $note_data, 2);
} else {
    $note_name = "";
    $note_title = "";
    $note_content = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete'])) {
    $note_title = trim($_POST['note_title']);
    $note_content = $_POST['note_content'];

    if ($note_name && in_array("notes/{$note_name}.txt", $notes)) {
        // Nếu 'note' đã tồn tại trong danh sách, sử dụng tên 'note' đó
        $note_file = "notes/{$note_name}.txt";
    } else {
        // Ngược lại, tạo một tên ngẫu nhiên mới
        $random_name = bin2hex(random_bytes(5));
        $note_file = "notes/{$random_name}.txt";
    }

    $note_data = "$note_title || $note_content";
    file_put_contents($note_file, $note_data);
    header("Location: ?note=" . basename($note_file, '.txt'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $note_name = $_GET['note'];
    if ($note_name) {
        $note_file = "notes/{$note_name}.txt";
        unlink($note_file);
    };
    header("Location: /");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title><?php echo "$note_title | ";?>NDHNote</title>
  <meta property="og:image" content="assets/images/2.png" />
  <link rel="icon" href="/assets/images/ndh.ico" />
</head>
<body>
<h1 style="text-align: center; margin-top: 12px;">NDHNote</h1>
<form method="post">
<div class="container">
    <div class="mb-3">
      <label class="form-label">Note name</label>
      <input type="text" name="note_title" class="form-control" value="<?php echo $note_title; ?>">
    </div>
    <div class="mb-3">
      <label for="note_content" class="form-label">Note content</label>
      <textarea class="form-control" rows="25" name="note_content" required><?php echo $note_content; ?></textarea>
    </div>
    <center>
      <input type="submit" value="Save Note" class="btn btn-primary">
      <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this note?');">Delete Note</button>
      <input type="button" onclick="window.location.href='/'" value="New Note" class="btn btn-success">
    </center>
</div>
</form>

<div class="container mt-5">
<?php if (count($notes) > 0) { ?>
  <center>
    <?php foreach ($notes as $note) {
        $note_data = file_get_contents($note);
        list($note_title, $note_content) = explode(' || ', $note_data, 2);
    ?>
      <button style="margin-top: 5px;" type="button" class="btn btn-warning" onclick="window.location.href='?note=<?php echo basename($note, '.txt'); ?>'"><i class="fa fa-folder"></i> <?php echo $note_title; ?></button>
    <?php } ?>
  </center>
  <br>
<?php } else { ?>
  <center>No notes found.</center>
<?php } ?>
</div>
<script>
document.addEventListener('keydown', function(e) {
  if (e.key == 's' && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
    e.preventDefault();
    document.forms[0].submit();
  }
});
</script>
</body>
</html>