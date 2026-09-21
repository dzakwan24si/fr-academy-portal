import { useState, useEffect, useCallback } from 'react';

export function useCulled() {
  const [data, setData] = useState([]);
  const [angkatanList, setAngkatanList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const fetchCulled = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      // 1. Fetch angkatan for dropdowns
      const resAngkatan = await fetch('/administrasi/monitoring/api/angkatan');
      const jsonAngkatan = await resAngkatan.json();
      if (jsonAngkatan.status === 'success') {
        setAngkatanList(jsonAngkatan.data || []);
      }

      // 2. Fetch culled
      const resCulled = await fetch('/administrasi/monitoring/api/culled');
      const jsonCulled = await resCulled.json();
      if (jsonCulled.status === 'success') {
        setData(jsonCulled.data || []);
      } else {
        throw new Error(jsonCulled.message || 'Gagal memuat data culled');
      }
    } catch (err) {
      console.error('Error fetching culled from Laravel API:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchCulled();
  }, [fetchCulled]);

  const addCulled = async (newCulled) => {
    try {
      const res = await fetch('/administrasi/monitoring/api/culled', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(newCulled),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchCulled();
        return json.data;
      }
      throw new Error(json.message || 'Gagal menambahkan data culled');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const updateCulled = async (id, updatedCulled) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/culled/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(updatedCulled),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchCulled();
        return json.data;
      }
      throw new Error(json.message || 'Gagal memperbarui data culled');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const deleteCulled = async (id) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/culled/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchCulled();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus data culled');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const deleteAllCulled = async () => {
    try {
      const res = await fetch('/administrasi/monitoring/api/culled/destroy-all', {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchCulled();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus seluruh data culled');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const importCulled = async (payload, mode = 'replace') => {
    try {
      let res;
      if (payload instanceof FormData) {
        res = await fetch('/administrasi/monitoring/api/culled/import', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
          },
          body: payload,
        });
      } else {
        res = await fetch('/administrasi/monitoring/api/culled/import', {
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
        fetchCulled();
        return json;
      }
      throw new Error(json.message || 'Gagal mengimport data culled');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  return { 
    data, 
    angkatanList,
    loading, 
    error, 
    addCulled, 
    updateCulled, 
    deleteCulled, 
    deleteAllCulled,
    importCulled,
    refetch: fetchCulled 
  };
}
