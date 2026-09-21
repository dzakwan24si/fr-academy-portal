import { useState, useEffect, useCallback } from 'react';

export function useAngkatan() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const fetchAngkatan = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetch('/administrasi/monitoring/api/angkatan');
      const json = await res.json();

      if (json.status === 'success') {
        setData(json.data || []);
      } else {
        throw new Error(json.message || 'Gagal memuat data angkatan');
      }
    } catch (err) {
      console.error('Error fetching angkatan from Laravel API:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchAngkatan();
  }, [fetchAngkatan]);

  const addAngkatan = async (newAngkatan) => {
    try {
      const res = await fetch('/administrasi/monitoring/api/angkatan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(newAngkatan),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchAngkatan();
        return json.data;
      }
      throw new Error(json.message || 'Gagal menambahkan angkatan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const updateAngkatan = async (id, updatedAngkatan) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/angkatan/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(updatedAngkatan),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchAngkatan();
        return json.data;
      }
      throw new Error(json.message || 'Gagal memperbarui angkatan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const deleteAngkatan = async (id) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/angkatan/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchAngkatan();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus angkatan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const importAngkatan = async (payload, mode = 'replace') => {
    try {
      setLoading(true);
      let res;
      if (payload instanceof FormData) {
        if (!payload.has('mode')) {
          payload.append('mode', mode);
        }
        res = await fetch('/administrasi/monitoring/api/angkatan/import', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
          },
          body: payload,
        });
      } else {
        res = await fetch('/administrasi/monitoring/api/angkatan/import', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
          },
          body: JSON.stringify({ rows: payload, mode }),
        });
      }

      const json = await res.json();
      if (json.status === 'success') {
        await fetchAngkatan();
        return json;
      }
      throw new Error(json.message || 'Gagal mengimport data angkatan');
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const deleteAllAngkatan = async () => {
    try {
      setLoading(true);
      const res = await fetch('/administrasi/monitoring/api/angkatan/destroy-all', {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        await fetchAngkatan();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus seluruh angkatan');
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  return { 
    data, 
    loading, 
    error, 
    addAngkatan, 
    updateAngkatan, 
    deleteAngkatan, 
    deleteAllAngkatan,
    importAngkatan,
    refetch: fetchAngkatan 
  };
}
