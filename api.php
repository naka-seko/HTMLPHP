<?php
$filename = 'jisyo_fruit.txt';
$english_words = [];

// ファイル読み込み関数
function load_dictionary($f_name) {
    $dictionary = [];
    if (file_exists($f_name)) {
        $lines = file($f_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            list($key, $value) = explode(',', $line, 2);
            $dictionary[trim($key)] = trim($value);
        }
    }
    return $dictionary;
}

// ファイル書き込み関数
function save_dictionary($f_name, $dictionary) {
    $file = fopen($f_name, "w");
    foreach ($dictionary as $key => $value) {
        fwrite($file, "$key,$value" . PHP_EOL);
    }
    fclose($file);
}

// 送られてきた JSON データの受け取り
$data = json_decode(file_get_contents("php://input"), true);
$action = $data["action"] ?? "";
$word = trim($data["word"] ?? "");
$meaning = trim($data["meaning"] ?? "");

$english_words = load_dictionary($filename);
$response = ["message" => "何も処理されませんでした。"];

// 操作ごとの処理
switch ($action) {
    case "search":
        if (array_key_exists($word, $english_words)) {
            $response["message"] = "{$word} の意味は「{$english_words[$word]}」です。";
        } else {
            $response["message"] = "{$word} は辞書に存在しません。";
        }
        break;

    case "save":
        $english_words[$word] = $meaning;
        save_dictionary($filename, $english_words);
        $response["message"] = "{$word} を辞書に保存しました。";
        break;

    case "delete":
        if (array_key_exists($word, $english_words)) {
            unset($english_words[$word]);
            save_dictionary($filename, $english_words);
            $response["message"] = "{$word} を削除しました。";
        } else {
            $response["message"] = "{$word} は辞書に存在しません。";
        }
        break;

    default:
        $response["message"] = "不正なアクションです。";
        break;
}

// JSON 形式で返す
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($response);
?>