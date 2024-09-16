import React from 'react';

function ResultsTable({ results }) {
    // resultsが配列であるかどうか確認
    if (!Array.isArray(results) || results.length === 0) {
        return <p>該当するレコードがありません。</p>;
    }

    const tableStyle = {
        borderCollapse: 'collapse',
        width: '100%',
    };

    const thTdStyle = {
        border: '1px solid black',
        padding: '8px',
    };

    const thStyle = {
        backgroundColor: '#f2f2f2',
    };

    return (
        <table style={tableStyle}>
            <thead>
                <tr>
                    <th style={{ ...thTdStyle, ...thStyle }}>UMIN試験ID</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>試験名</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>対象疾患名/Condition</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>目的1/Narrative objectives1</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>目的2/Basic objectives2</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>目的2 -その他詳細/Basic objectives -Others</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>試験のフェーズ/Developmental phase</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>主要アウトカム評価項目/Primary outcomes</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>副次アウトカム評価項目/Key secondary outcomes</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>試験の種類/Study type</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>試験デザイン/Study design</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>年齢（下限）/Age-lower limit</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>年齢（上限）/Age-upper limit</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>性別/Gender</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>選択基準/Key inclusion criteria</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>除外基準/Key exclusion criteria</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>目標参加者数/Target sample size</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>責任研究者/Name of lead principal investigator</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>所属組織/Organization</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>所属部署/Division name</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>郵便番号/Zip code</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>住所/Address</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>電話/TEL</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>Email/Email</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>実施責任組織/Sponsor</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>研究費提供組織/Funding Source</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>IRB等連絡先（公開）/IRB Contact (For public release)</th>
                    <th style={{ ...thTdStyle, ...thStyle }}>試験実施施設/Institutions</th>
                </tr>
            </thead>
            <tbody>
                {results.map((result) => (
                    <tr key={result.umin_id}>
                        <td style={thTdStyle}>{result.umin_with_date}</td>
                        <td style={thTdStyle}>{result.scientific_title}</td>
                        <td style={thTdStyle}>{result.condition}</td>
                        <td style={thTdStyle}>{result.narrative_objectives1}</td>
                        <td style={thTdStyle}>{result.basic_objectives2}</td>
                        <td style={thTdStyle}>{result.basic_objectives_others}</td>
                        <td style={thTdStyle}>{result.developmental_phase}</td>
                        <td style={thTdStyle}>{result.primary_outcomes}</td>
                        <td style={thTdStyle}>{result.key_secondary_outcomes}</td>
                        <td style={thTdStyle}>{result.study_type}</td>
                        <td style={thTdStyle}>{result.study_design}</td>
                        <td style={thTdStyle}>{result.age_lower_limit}</td>
                        <td style={thTdStyle}>{result.age_upper_limit}</td>
                        <td style={thTdStyle}>{result.gender}</td>
                        <td style={thTdStyle}>{result.key_inclusion_criteria}</td>
                        <td style={thTdStyle}>{result.key_exclusion_criteria}</td>
                        <td style={thTdStyle}>{result.target_sample_size}</td>
                        <td style={thTdStyle}>{result.responsible_person}</td>
                        <td style={thTdStyle}>{result.responsible_organization}</td>
                        <td style={thTdStyle}>{result.responsible_division}</td>
                        <td style={thTdStyle}>{result.responsible_zipCode}</td>
                        <td style={thTdStyle}>{result.responsible_address}</td>
                        <td style={thTdStyle}>{result.responsible_tel}</td>
                        <td style={thTdStyle}>{result.responsible_email}</td>
                        <td style={thTdStyle}>{result.institute}</td>
                        <td style={thTdStyle}>{result.organization}</td>
                        <td style={thTdStyle}>{result.irb_organization}</td>
                        <td style={thTdStyle}>{result.institutions}</td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

export default ResultsTable;
