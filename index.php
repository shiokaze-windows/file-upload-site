<?php
$upload_dir = 'uploads/'; // アップロードされたファイルを保存するディレクトリ

// ファイルがアップロードされた場合
if(isset($_FILES['file'])){
    $file = $_FILES['file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(end(explode('.', $file_name))); // ファイルの拡張子を取得
    
    // ファイル名が被らないように変更する
    $file_name = time() . "_" . $file_name;
    // ファイルをアップロードする
    move_uploaded_file($file_tmp, $upload_dir . $file_name);
}

// アップロードされたファイルの一覧を取得する
$files = array_diff(scandir($upload_dir), array('..', '.'));

// ファイルが削除された場合
if(isset($_POST['delete'])){
    $file_name = $_POST['delete'];
    unlink($upload_dir . $file_name); // ファイルを削除する
}

// ファイルがダウンロードされた場合
if(isset($_GET['download'])){
    $file_name = $_GET['download'];
    $file_path = $upload_dir . $file_name;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path); // ファイルをダウンロードする
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ファイルアップロード</title>
<style>
    body {
        font-family: sans-serif;
        font-size: 16px;
    }

    h1 {
        margin-bottom: 20px;
    }

    form {
        margin-bottom: 20px;
    }

    input[type="file"] {
        display: none;
    }

    label[for="file"] {
        display: inline-block;
        padding: 10px 20px;
        background-color: #337ab7;
        color: #fff;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    label[for="file"]:hover {
        background-color: #286090;
    }

    .file-name {
        margin-left: 10px;
        font-size: 14px;
    }

    .progress {
        height: 20px;
        margin-bottom: 20px;
        overflow: hidden;
        background-color: #f5f5f5;
        border-radius: 4px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        float: left;
        width: 0%;
        height: 100%;
        font-size: 12px;
        line-height: 20px;
        color: #fff;
        text-align: center;
        background-color: #337ab7;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
        transition: width 0.6s ease;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    td:last-child {
        text-align: center;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #337ab7;
        color: #fff;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn:hover {
        background-color: #286090;
    }

    .btn-primary {
        background-color: #337ab7;
    }

    .btn-primary:hover {
        background-color: #286090;
    }

    .btn-danger {
        background-color: #d9534f;
    }

    .btn-danger:hover {
        background-color: #c9302c;
    }
</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            // ファイルがアップロードされたら、ファイル名を表示する
            $('input[type=file]').change(function(){
                var file_name = $(this).val().split('\\').pop();
                $('.file-name').text(file_name);
            });
            // ファイル
        // アップロードフォームを送信する
        $('form').submit(function(event){
            event.preventDefault();
            var form_data = new FormData(this);
            $.ajax({
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(event){
                        if(event.lengthComputable){
                            var percent = Math.round((event.loaded / event.total) * 100);
                            $('.progress-bar').css('width', percent + '%');
                            $('.progress-bar').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                type: 'POST',
                url: 'index.php',
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response){
                    location.reload();
                }
            });
        });
    });
</script>
<style>
body {
  background-image: url(56562.png);
background-size: cover;
background-repeat: no-repeat;
background-attachment: fixed;
}
    .progress {
        height: 20px;
        margin-bottom: 20px;
        overflow: hidden;
        background-color: #f5f5f5;
        border-radius: 4px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        float: left;
        width: 0%;
        height: 100%;
        font-size: 12px;
        line-height: 20px;
        color: #fff;
        text-align: center;
        background-color: #337ab7;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.15);
        transition: width 0.6s ease;
    }
</style>
</head>
<body>
    <h1 style="color:#FF8C00">ファイルアップロード</h1>
    <h4 style="color:#FF8C00">IISの上限で4GBまでアップロードできます<br>サーバーのストレージは500GB利用できます</h4>
    <form method="post" enctype="multipart/form-data">
        <div>
            <input type="file" name="file" id="file" style="display:none;">
            <label for="file" class="btn btn-default">ファイルを選択</label>
            <span class="file-name"></span>
        </div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
        <button type="submit" class="btn btn-primary">アップロード</button>
    </form>
    <h2>アップロードされたファイル一覧</h2>
    <table>
        <thead>
            <tr>
                <th>ファイル名</th>
                <th>サイズ</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($files as $file): ?>
            <tr>
                <td><?= $file ?></td>
                <td><?= format_filesize(filesize($upload_dir . $file)) ?></td>
                <td>
                    <a href="<?= 'uploads/' . $file ?>" target="_blank">プレビュー</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete" value="<?= $file ?>">
                        <button type="submit" class="btn btn-danger">削除</button>
                    </form>
                    <a href="<?= 'index.php?download=' . $file ?>" class="btn btn-primary">ダウンロード</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
<?php
// アップロード先のディレクトリ
$upload_dir = 'uploads/';

// アップロードされたファイルを保存する
if(isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK){
    $file = $_FILES['file']['name'];
    $file_ext = pathinfo($file, PATHINFO_EXTENSION);
    $file_base = pathinfo($file, PATHINFO_FILENAME);
    $i = 1;
    while(file_exists($upload_dir . $file)){
        $file = $file_base . '_' . $i . '.' . $file_ext;
        $i++;
    }
    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $file);
}

// ファイルを削除する
if(isset($_POST['delete'])){
    $file = $_POST['delete'];
    unlink($upload_dir . $file);
}

// ファイルをダウンロードする
if(isset($_GET['download'])){
    $file = $_GET['download'];
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Length: ' . filesize($upload_dir . $file));
    readfile($upload_dir . $file);
    exit;
}

// アップロードされたファイル一覧を取得する
$files = array_diff(scandir($upload_dir), array('..', '.'));

// ファイルサイズをKB単位に変換する関数
function format_filesize($size){
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while($size >= 1024 && $i < count($units) - 1){ // ファイルサイズが1TB未満の場合は単位を変換しながらループ
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
?>