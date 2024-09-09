<?php
// エラー表示
ini_set("display_errors", 1);

// 1. DB接続します
try {
    // Password:MAMP='root',XAMPP=''
    $pdo = new PDO('mysql:dbname=gs_db;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
    exit('DBerror:'.$e->getMessage());
}

// 2. データ取得SQL作成
$sql = "SELECT id, title, url, bookmark FROM qiita_table ORDER BY bookmark DESC, id ASC";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute(); // 実行

// 3. データ表示
if($status==false) {
    // execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC[カラム名のみで取得できるモード

// HTMLとCSSを追加して表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>データ表示</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .bookmark {
            cursor: pointer;
            color: blue;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleBookmark(id) {
            $.ajax({
                url: 'update_bookmark.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response == 'success') {
                        location.reload();
                    } else {
                        alert('更新に失敗しました。');
                    }
                }
            });
        }
    </script>
</head>
<body>
    <h1>Qiita お勧め記事</h1>
    <table>
        <tr>
            <th>Title</th>
            <th>URL</th>
            <th>ブックマーク</th>
        </tr>
        <?php foreach ($values as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') ?></td>
                <td><a href="<?= htmlspecialchars($row["url"], ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars($row["url"], ENT_QUOTES, 'UTF-8') ?></a></td>
                <td>
                    <?php if ($row["bookmark"] == 1): ?>
                        <button class="bookmark" onclick="toggleBookmark(<?= $row['id'] ?>)">★</button>
                    <?php else: ?>
                        <button class="bookmark" onclick="toggleBookmark(<?= $row['id'] ?>)">未ブックマーク</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>