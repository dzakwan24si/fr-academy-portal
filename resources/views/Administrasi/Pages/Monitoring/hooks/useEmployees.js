import { useState, useEffect, useRef } from 'react';
import { supabase } from '../lib/supabase';

export function useEmployees(statusFilter = null) {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchEmployees = async () => {
    setLoading(true);
    setError(null);
    try {
      let allData = [];
      let currentOffset = 0;
      const pageSize = 1000;
      let hasMore = true;

      while (hasMore) {
        let query = supabase
          .from('employees')
          .select('*')
          .order('ID_MONITORING', { ascending: false })
          .range(currentOffset, currentOffset + pageSize - 1);
        
        if (statusFilter) {
          if (Array.isArray(statusFilter)) {
            query = query.in('STATUS', statusFilter);
          } else {
            query = query.eq('STATUS', statusFilter);
          }
        }

        const { data: chunk, error } = await query;
        if (error) throw error;
        
        allData = [...allData, ...chunk];
        
        if (chunk.length < pageSize) {
          hasMore = false;
        } else {
          currentOffset += pageSize;
        }
      }

      setData(allData);
    } catch (err) {
      console.error("Error fetching employees:", err.message);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const filterKey = JSON.stringify(statusFilter);
  const hasFetched = useRef(false);

  useEffect(() => {
    if (!hasFetched.current) {
      fetchEmployees();
      hasFetched.current = true;
    }
  }, [filterKey]);

  const addEmployee = async (newEmployee) => {
    const { data: inserted, error } = await supabase
      .from('employees')
      .insert([newEmployee])
      .select();
    
    if (error) throw error;
    setData([inserted[0], ...data]);
    return inserted[0];
  };

  const updateEmployee = async (id, updatedEmployee) => {
    const { data: updated, error } = await supabase
      .from('employees')
      .update(updatedEmployee)
      .eq('ID_MONITORING', id)
      .select();
      
    if (error) throw error;
    setData(data.map(emp => emp.ID_MONITORING === id ? updated[0] : emp));
    return updated[0];
  };

  const deleteEmployee = async (id) => {
    const { error } = await supabase
      .from('employees')
      .delete()
      .eq('ID_MONITORING', id);
      
    if (error) throw error;
    setData(data.filter(emp => emp.ID_MONITORING !== id));
  };

  return { 
    data, 
    loading, 
    error, 
    addEmployee, 
    updateEmployee, 
    deleteEmployee, 
    refetch: fetchEmployees 
  };
}
