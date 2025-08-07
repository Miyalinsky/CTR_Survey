// frontend/src/components/SearchForm.js
import React, { useState } from 'react';
import './SearchForm.css';

function SearchForm({ onSearch }) {
    const [keyword, setKeyword] = useState('');
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        onSearch({ keyword, startDate, endDate });
    };

    return (
        <form className="search-form" onSubmit={handleSubmit}>
            <input
                type="text"
                id="keyword"
                placeholder="キーワード"
                className="search-input"
                value={keyword}
                onChange={(e) => setKeyword(e.target.value)}
            />
            <input
                type="date"
                id="startDate"
                className="date-input"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
            />
            <input
                type="date"
                id="endDate"
                className="date-input"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
            />
            <button className="search-button" type="submit">検索</button>
        </form>
    );
}

export default SearchForm;
