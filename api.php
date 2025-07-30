<?php
header("Content-Type: application/json");

// 入力を受け取る
$input = json_decode(file_get_contents("php://input"), true);
$year = $input["year"] ?? null;
$month = $input["month"] ?? null;
// 年の範囲
$min_year = 1950;
$max_year = 2099;
// 入力の検証
if ($year === null || $month === null || $year < $min_year || $year > $max_year || $month < 1 || $month > 12) {
    echo json_encode(["error" => "入力が不正です。"]);
    exit;
}

// カレンダー生成
function generate_calendar($year, $month) {
    $calendar = [];
    $first_day = mktime(0, 0, 0, $month, 1, $year);
    $days_in_month = date('t', $first_day);
    $first_weekday = date('w', $first_day);

    // 初期空白
    $week = array_fill(0, $first_weekday, "");
    for ($day = 1; $day <= $days_in_month; $day++) {
        $week[] = $day;
        if (count($week) === 7) {
            $calendar[] = $week;
            $week = [];
        }
    }
    // 残りの日付
    if (!empty($week)) {
        $week = array_pad($week, 7, "");
        $calendar[] = $week;
    }
    return $calendar;
}

$calendar = generate_calendar($year, $month);

// 結果をJSONで返す
echo json_encode([
    "year" => $year,
    "month" => $month,
    "calendar" => $calendar
]);
?>
