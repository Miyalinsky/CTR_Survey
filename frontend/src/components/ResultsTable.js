import React from 'react';

function ResultsTable({ results }) {
    // resultsが配列であるかどうか確認
    if (!Array.isArray(results) || results.length === 0) {
        return <p>該当するレコードがありません。</p>;
    }

    return (
        <table>
            <thead>
                <tr>
                    <th>UMIN試験ID</th>
                    <th>科学的試験名</th>
                    <th>対象疾患名</th>
                    <th>一般公開日</th>
                </tr>
            </thead>
            <tbody>
                {results.map((result) => (
                    <tr key={result.umin_id}>
                        <td>{result.umin_id}</td>
                        <td>{result.scientific_title}</td>
                        <td>{result.condition}</td>
                        <td>{result.date_of_disclosure}</td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

export default ResultsTable;
