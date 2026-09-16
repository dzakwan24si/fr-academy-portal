import { useState, useEffect, useCallback } from 'react';

export function useAfdeling() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const fetchAfdeling = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await fetch('/administrasi/monitoring/api/afdeling');
      const json = await res.json();

      if (json.status === 'success') {
        setData(json.data || []);
      } else {
        throw new Error(json.message || 'Gagal memuat data afdeling');
      }
    } catch (err) {
      console.error('Error fetching afdeling from Laravel API:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchAfdeling();
  }, [fetchAfdeling]);

  const updateAfdeling = async (regionMap) => {
    try {
      const res = await fetch('/administrasi/monitoring/api/afdeling', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        body: JSON.stringify(regionMap),
      });
      const json = await res.json();
      if (json.status === 'success') {
        setData(json.data || []);
        return json.data;
      }
      throw new Error(json.message || 'Gagal memperbarui afdeling');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  return { 
    data, 
    loading, 
    error, 
    updateAfdeling, 
    refetch: fetchAfdeling 
  };
}
