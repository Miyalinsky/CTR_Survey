import React, { useState } from 'react';
import SearchForm from './components/SearchForm';
import ResultsTable from './components/ResultsTable';
import { searchTrials, exportTrials, updateDatabase } from './api';

function App() {
  const [results, setResults] = useState([]);
  const [searchParams, setSearchParams] = useState({
    keyword: '',
    startDate: '',
    endDate: ''
  });

  const handleSearch = async (params) => {
    try {
      const data = await searchTrials(params);
      setResults(data);
      setSearchParams(params); // 検索パラメータを保存しておく
    } catch (error) {
      console.error('検索エラー:', error);
    }
  };

  const handleExport = async () => {
    try {
      const blob = await exportTrials(searchParams); // 保存した検索パラメータを使用
      const url = window.URL.createObjectURL(new Blob([blob]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'trials.xlsx');
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
    } catch (error) {
      console.error('エクスポートエラー:', error);
    }
  };

  const handleUpdateDatabase = async () => {
    try {
      const response = await updateDatabase();
      alert('データベースが更新されました！');
    } catch (error) {
      console.error('データベース更新エラー:', error);
      alert('データベース更新に失敗しました。');
    }
  };

  return (
    <div className="App">
      <h1>UMIN-CTR検索システム</h1>
      <SearchForm onSearch={handleSearch} />
      <button onClick={handleExport}>Excelに保存</button>
      <button onClick={handleUpdateDatabase}>データベースを更新</button>
      <ResultsTable results={results || []} />
    </div>
  );
}

export default App;
