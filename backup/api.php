<?php
$filename = 'jisyo_fruit.txt';
$english_words = [];

// ファイル読み込み関数
function load_dictionary($f_name) {
    // ファイルチェック処理
    $dictionary = [];
    // ファイルが存在しない場合の新規作成対応
    if (!file_exists($f_name)) {
        $file = fopen($f_name, "w"); // 新規ファイル作成
        fclose($file);
    }
    // １行ごとに読み込んで、配列に格納
    $lines = file($f_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($key, $value) = explode(',', $line, 2);
        $dictionary[trim($key)] = trim($value);
    }
    // 配列を返す
    return $dictionary;
}

// ファイル書き込み関数
function save_dictionary($f_name, $dictionary) {
    // ファイルチェック処理
    if (!file_exists($f_name)) {
        throw new Exception("ファイルが見つかりません: $f_name");
    }

    // 配列からファイル書き出し処理
    $file = fopen($f_name, "w");
    if ($file === false) {
        throw new Exception("ファイルを開くことが出来ませんでした: $f_name");
    }

    foreach ($dictionary as $key => $value) {
        fwrite($file, "$key,$value" . PHP_EOL);
    }

    fclose($file);
}

// 送られてきた JSON データの受け取り
$data = json_decode(file_get_contents("php://input"), true);
// 初期設定
$action = $data["action"] ?? "";
$word = trim($data["word"] ?? "");
$meaning = trim($data["meaning"] ?? "");

$english_words = load_dictionary($filename);
$response = ["message" => "何も処理されませんでした。"];

// 操作ごとの処理
switch ($action) {
    // 検索時
    case "search":
        if (!is_array($english_words)) {
            echo json_encode(["status" => "error","message" => "辞書データが正しくありません。"]);
            return;
        }

        //　辞書に存在すれば、日本語で回答してJSONに返す。
        //　存在しなければ、見つからない表示してJSONに返す。
        if (array_key_exists($word, $english_words)) {
            $response = [
                "status" => "success",
                "message" => "{$word} の意味は「{$english_words[$word]}」です。"
            ];
        } else {
            $response = [
                "status" => "notfound",
                "message"=> "お探しの単語 {$word} は見つかりませんでした。😢"
            ];
        }
        break;

    // 保存時
    case "save":
        //　辞書に存在すれば、更新保存してJSONに返す。
        //　存在しなければ、追加保存してJSONに返す。
        if (array_key_exists($word, $english_words)) {
            $response["message"] = "{$word} は辞書に存在します。更新保存しました。";
        } else {
            $response["message"] = "{$word} は辞書に存在しません。追加保存しました。";
        }

        $english_words[$word] = $meaning;
        save_dictionary($filename, $english_words);
        break;

    // 削除時
    case "delete":
        //　辞書に存在すれば、辞書削除してJSONに返す。
        //　存在しなければ、存在しない表示してJSONに返す。
        if (array_key_exists($word, $english_words)) {
            unset($english_words[$word]);
            save_dictionary($filename, $english_words);
            $response["message"] = "{$word} を削除しました。";
        } else {
            $response["message"] = "{$word} は辞書に存在しません。";
        }
        break;

    // 上記以外、不正アクション
        default:
        $response["message"] = "不正なアクションです。";
        break;

}
// JSON 形式で返す
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
?>