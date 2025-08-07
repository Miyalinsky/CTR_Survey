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
  const [loading, setLoading] = useState(false); // ローディングステート追加
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
      await logout();
      setIsAuthenticated(false);
    } catch (error) {
      console.error('ログアウトエラー:', error);
    }
  };

  const handleSearch = async (params) => {
    try {
      setLoading(true); // ローディング開始
      const data = await searchTrials(params);
      setResults(data);
      setSearchParams(params);
    } catch (error) {
      console.error('検索エラー:', error);
    } finally {
      setLoading(false); // ローディング終了
    }
  };

  const handleExport = async () => {
    try {
      setLoading(true); // ローディング開始
      const params = {
        keyword: document.getElementById("keyword").value,
        startDate: document.getElementById("startDate").value,
        endDate: document.getElementById("endDate").value
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
    } finally {
      setLoading(false); // ローディング終了
    }
  };

  const handleAllExport = async () => {
    try {
      setLoading(true); // ローディング開始
      const params = {
        startDate: document.getElementById("startDate").value,
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
    } finally {
      setLoading(false); // ローディング終了
    }
  };

  const handleUpdateDatabase = async () => {
    try {
      setLoading(true); // ローディング開始
      await updateDatabase();
      alert('データベースが更新されました！');
    } catch (error) {
      console.error('データベース更新エラー:', error);
      alert('データベース更新に失敗しました。');
    } finally {
      setLoading(false); // ローディング終了
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
        {loading ? (
          <div className="loading-animation">Now loading...</div> // ローディング中の表示
        ) : (
          <div className="search-section">
            <SearchForm onSearch={handleSearch} />
            <div className="button-group">
              <button className="export-button" onClick={handleExport}>Excelに保存</button>
              <button className="export-button" onClick={handleAllExport}>All Export</button>
              {isAdmin === 1 && (<button className="update-button" onClick={handleUpdateDatabase}>データベースを更新</button>)}
            </div>
            <ResultsTable results={results || []} />
          </div>
        )}
      </main>
    </div>
  );
}

export default App;
