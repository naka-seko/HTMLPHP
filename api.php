<?php
$filename = 'jisyo_fruit.txt';
$english_words = [];

// ファイル読み込み関数
function load_dictionary($f_name) {
    $dictionary = [];
    if (!file_exists($f_name)) {
        throw new Exception("ファイルが見つかりません: $f_name");
    }
    $lines = file($f_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($key, $value) = explode(',', $line, 2);
        $dictionary[trim($key)] = trim($value);
    }

    return $dictionary;
}

// ファイル書き込み関数
function save_dictionary($f_name, $dictionary) {
    if (!file_exists($f_name)) {
        throw new Exception("ファイルが見つかりません: $f_name");
    }
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
        if (!is_array($english_words)) {
            echo json_encode(["status" => "error", "message" => "辞書データが正しくありません。"]);
            break;
        }

        if (array_key_exists($word, $english_words)) {
            $response = [
                "status" => "success",
                "message" => "{$word} の意味は「{$english_words[$word]}」です。"
            ];
        } else {
            $response = [
                "status" => "error",
                "message"=> "お探しの単語 {$word} は見つかりませんでした。😢"
            ];
        }
        break;

    case "save":
        $is_word_empty = empty($word);
        $is_meaning_empty = empty($meaning);
        if ($is_word_empty || $is_meaning_empty) {
            $response["message"] = "キー（英語）又は日本語が空の為、保存出来ません。";
            break;
        }

        if (array_key_exists($word, $english_words)) {
            $response["message"] = "{$word} は辞書に存在します。更新保存しました。";
        } else {
            $response["message"] = "{$word} は辞書に存在しません。追加保存しました。";
        }
        $english_words[$word] = $meaning;
        save_dictionary($filename, $english_words);
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
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>