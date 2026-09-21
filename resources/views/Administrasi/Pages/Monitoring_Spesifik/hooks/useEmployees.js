import { useState, useEffect, useCallback } from 'react';

export function useEmployees(statusFilter = null) {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const fetchEmployees = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const params = new URLSearchParams();
      if (statusFilter) {
        if (Array.isArray(statusFilter)) {
          statusFilter.forEach(s => params.append('status[]', s));
        } else {
          params.append('status', statusFilter);
        }
      }

      const res = await fetch(`/administrasi/monitoring/api/employees?${params.toString()}`);
      const json = await res.json();

      if (json.status === 'success') {
        setData(json.data || []);
      } else {
        throw new Error(json.message || 'Gagal memuat data karyawan');
      }
    } catch (err) {
      console.error('Error fetching employees from Laravel API:', err);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [JSON.stringify(statusFilter)]);

  useEffect(() => {
    fetchEmployees();
  }, [fetchEmployees]);

  const addEmployee = async (newEmployee) => {
    try {
      const res = await fetch('/administrasi/monitoring/api/employees', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(newEmployee),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchEmployees();
        return json.data;
      }
      throw new Error(json.message || 'Gagal menambahkan karyawan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const updateEmployee = async (id, updatedEmployee) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/employees/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
        body: JSON.stringify(updatedEmployee),
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchEmployees();
        return json.data;
      }
      throw new Error(json.message || 'Gagal memperbarui karyawan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const deleteEmployee = async (id) => {
    try {
      const res = await fetch(`/administrasi/monitoring/api/employees/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchEmployees();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus karyawan');
    } catch (err) {
      setError(err.message);
      throw err;
    }
  };

  const importEmployees = async (payload, mode = 'replace') => {
    try {
      setLoading(true);
      let res;
      if (payload instanceof FormData) {
        if (!payload.has('mode')) {
          payload.append('mode', mode);
        }
        res = await fetch('/administrasi/monitoring/api/employees/import', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json',
          },
          body: payload,
        });
      } else {
        res = await fetch('/administrasi/monitoring/api/employees/import', {
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
        await fetchEmployees();
        return json;
      }
      throw new Error(json.message || 'Gagal mengimport data karyawan');
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const deleteAllEmployees = async () => {
    try {
      setLoading(true);
      const res = await fetch('/administrasi/monitoring/api/employees/destroy-all', {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        await fetchEmployees();
        return true;
      }
      throw new Error(json.message || 'Gagal menghapus seluruh karyawan');
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const resetToDefault = async () => {
    try {
      const res = await fetch('/administrasi/monitoring/api/reset-data', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': getCsrfToken(),
          'Accept': 'application/json',
        },
      });
      const json = await res.json();
      if (json.status === 'success') {
        fetchEmployees();
      }
    } catch (err) {
      setError(err.message);
    }
  };

  return { 
    data, 
    loading, 
    error, 
    addEmployee, 
    updateEmployee, 
    deleteEmployee, 
    deleteAllEmployees,
    importEmployees,
    refetch: fetchEmployees,
    resetToDefault
  };
}
