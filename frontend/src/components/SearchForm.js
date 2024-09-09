// frontend/src/components/SearchForm.js
import React, { useState } from 'react';

function SearchForm({ onSearch }) {
    const [keyword, setKeyword] = useState('');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        onSearch({ keyword, startDate, endDate });
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                type="text"
                placeholder="キーワード"
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
            />
            <input
                type="date"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
            />
            <input
                type="date"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
            />
            <button type="submit">検索</button>
        </form>
    );
}

export default SearchForm;
