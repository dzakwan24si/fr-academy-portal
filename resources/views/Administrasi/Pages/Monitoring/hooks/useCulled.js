import { useState, useEffect, useRef } from 'react';
import { supabase } from '../lib/supabase';

export function useCulled() {
  const [data, setData] = useState([]);
  const [angkatanList, setAngkatanList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchCulled = async () => {
    setLoading(true);
    setError(null);
    try {
      // 1. Fetch data angkatan for the dropdown
      const { data: angkatanData, error: angkatanError } = await supabase
        .from('angkatan')
        .select('*')
        .order('id_angkatan', { ascending: false });
        
      if (angkatanError) throw angkatanError;
      setAngkatanList(angkatanData || []);

      // 2. Fetch culled data joined with angkatan
      const { data: culledData, error: culledError } = await supabase
        .from('culled')
        .select('*, angkatan(*)')
        .order('id_culled', { ascending: false });

      if (culledError) throw culledError;
      setData(culledData || []);

    } catch (err) {
      console.error("Error fetching culled data:", err.message);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const hasFetched = useRef(false);

  useEffect(() => {
    if (!hasFetched.current) {
      fetchCulled();
      hasFetched.current = true;
    }
  }, []);

  const addCulled = async (newCulled) => {
    let nextId = 1;
    if (data && data.length > 0) {
      nextId = Math.max(...data.map(item => item.id_culled || 0)) + 1;
    }

    const { data: inserted, error } = await supabase
      .from('culled')
      .insert([{ ...newCulled, id_culled: nextId }])
      .select('*, angkatan(*)');
    
    if (error) throw error;
    setData([inserted[0], ...data]);
    return inserted[0];
  };

  const updateCulled = async (id, updatedCulled) => {
    const { data: updated, error } = await supabase
      .from('culled')
      .update(updatedCulled)
      .eq('id_culled', id)
      .select('*, angkatan(*)');
      
    if (error) throw error;
    setData(data.map(item => item.id_culled === id ? updated[0] : item));
    return updated[0];
  };

  const deleteCulled = async (id) => {
    const { error } = await supabase
      .from('culled')
      .delete()
      .eq('id_culled', id);
      
    if (error) throw error;
    setData(data.filter(item => item.id_culled !== id));
  };

  return { 
    data, 
    angkatanList,
    loading, 
    error, 
    addCulled, 
    updateCulled, 
    deleteCulled, 
    refetch: fetchCulled 
  };
}
