// frontend/src/api.js
export async function searchTrials({ keyword, startDate, endDate }) {
    const response = await fetch(`http://localhost/CTR_Survey/backend/api/search.php?keyword=${encodeURIComponent(keyword)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`);
    if (!response.ok) {
        throw new Error('サーチレスポンスエラー');
    }
    return response.json();
}

export async function exportTrials({ keyword, startDate, endDate }) {
    const response = await fetch(`http://localhost/CTR_Survey/backend/api/export.php?keyword=${encodeURIComponent(keyword)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`);
    if (!response.ok) {
        throw new Error('エクスポートレスポンスエラー');
    }
    return response.blob();
}

// データベースを更新するための関数
export async function updateDatabase() {
    const response = await fetch('http://localhost/CTR_Survey/backend/scripts/fetch_csv.php');
    if (!response.ok) {
        throw new Error('DBアップデートエラー');
    }
    const result = await response.json();
    return result;
}
