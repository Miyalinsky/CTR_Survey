import React, { useState } from 'react';
import SearchForm from './components/SearchForm';
import ResultsTable from './components/ResultsTable';
import { searchTrials, exportTrials, exportAllTrials, updateDatabase } from './api';
import Login from './components/Login';
import { logout } from './api';
import './App.css';

function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false);
  const [results, setResults] = useState([]);
  const [searchParams, setSearchParams] = useState({
    keyword: '',
    startDate: '',
    endDate: ''
  });

  const handleLogin = (isAdminFlag) => {
    setIsAuthenticated(true);
    setIsAdmin(isAdminFlag);
  };

  const handleLogout = async () => {
    try {
      await logout(); // APIでログアウト処理を呼び出す
      setIsAuthenticated(false); // 認証状態をリセット
    } catch (error) {
      console.error('ログアウトエラー:', error);
    }
  };

  const handleSearch = async (params) => {
    try {
      const data = await searchTrials(params);
      setResults(data);
      setSearchParams(params);
    } catch (error) {
      console.error('検索エラー:', error);
    }
  };

  const handleExport = async () => {
    try {
      const params = {
        keyword: document.getElementById("keyword").value,
        startDate: document.getElementById("startDate").value,  // startDateをDOMから直接取得
        endDate: document.getElementById("endDate").value  // endDateをDOMから直接取得
      };
      const blob = await exportTrials(params);
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

  const handleAllExport = async () => {
    try {
      const params = {
        startDate: document.getElementById("startDate").value,  // DOMから直接取得
        endDate: document.getElementById("endDate").value
      };
      const blob = await exportAllTrials(params);
      const url = window.URL.createObjectURL(new Blob([blob]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'all_trials.xlsx');
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
    } catch (error) {
      console.error('エクスポートエラー:', error);
    }
  };

  if (!isAuthenticated) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <div className="App">
      <header>
        <h1>UMIN-CTR検索システム</h1>
        <button className="logout-button" onClick={handleLogout}>ログアウト</button>
      </header>

      <main>
        <div className="search-section">
          <SearchForm onSearch={handleSearch} />
          <div className="button-group">
            <button className="export-button" onClick={handleExport}>Excelに保存</button>
            <button className="export-button" onClick={handleAllExport}>All Export</button>
            {isAdmin && <button className="update-button" onClick={handleUpdateDatabase}>データベースを更新</button>}
          </div>
        </div>

        <ResultsTable results={results || []} />
      </main>
    </div>
  );
}

export default App;
