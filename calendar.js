document.addEventListener("DOMContentLoaded", () => {
    const createButton = document.getElementById("generate-calendar");
    if (!createButton) {
        return; // 処理を中断
    }

    createButton.addEventListener("click", async (event) => {
        event.preventDefault(); // デフォルトのフォーム送信を防ぐ
        // 年と月のドロップダウンから値を取得
        const year = document.getElementById("year").value;
        const month = document.getElementById("month").value;

        try {
            // Fetchリクエストに年と月を含めて送信
            const response = await fetch("api.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ year, month }), // 年と月を送信
            });
            // レスポンスのチェック
            if (!response.ok) throw new Error("APIエラー: " + response.statusText);

            const data = await response.json();
            console.log("APIからのデータ:", data);
            if (!data || !data.calendar) {
                console.error("APIからのデータが不正です。");
                return;
            }

            const calendar = data.calendar || [];
            const table = document.createElement("table");
            table.style.borderCollapse = "collapse";

            // 曜日行を生成
            const daysOfWeek = ["日", "月", "火", "水", "木", "金", "土"];
            const headerRow = document.createElement("tr");
            daysOfWeek.forEach(day => {
                const th = document.createElement("th");
                th.textContent = day;
                th.style.border = "1px solid #ddd";
                th.style.padding = "8px";
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            calendar.forEach(week => {
                const row = document.createElement("tr");
                week.forEach(day => {
                    const cell = document.createElement("td");
                    cell.textContent = day || "";
                    cell.style.border = "1px solid #ddd";
                    row.appendChild(cell);
                });
                table.appendChild(row);
            });

            // HTMLの #output-section にテーブルを追加
            const outputSection = document.getElementById("output-section");
            if (outputSection.children.length === 0) {
                // 必要に応じて生成する処理
                const newTable = document.createElement('table');
                newTable.innerHTML = '<tr><td>データなし</td></tr>';
                outputSection.appendChild(newTable);
                console.info("仮テーブルを生成しました。");
            }
            outputSection.innerHTML = ""; // 前回のデータをクリア
            outputSection.appendChild(table);

        } catch (error) {
            console.error("エラー発生:", error);
        }
    });
});
