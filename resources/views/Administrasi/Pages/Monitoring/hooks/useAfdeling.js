import { useState, useEffect, useRef } from 'react';
import { supabase } from '../lib/supabase';

const normalizeReg = (value = "") => {
  const region = String(value).toLowerCase();
  if (region.includes("riau")) return "Riau";
  if (region.includes("kalbar") || region.includes("kalimantan barat")) return "Kalbar";
  if (region.includes("kubar") || region.includes("kalimantan timur")) return "Kubar";
  if (region.includes("corp") || region.includes("pusat") || region.includes("jakarta")) return "Corp";
  return null;
};

export function useAfdeling() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchAfdeling = async () => {
    setLoading(true);
    setError(null);
    try {
      const { data: afdelingData, error: afdelingError } = await supabase
        .from('afdeling')
        .select('*');

      if (afdelingError) throw afdelingError;
      setData(afdelingData || []);
    } catch (err) {
      console.error("Error fetching afdeling data:", err.message);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const hasFetched = useRef(false);

  useEffect(() => {
    if (!hasFetched.current) {
      fetchAfdeling();
      hasFetched.current = true;
    }
  }, []);

  const updateAfdeling = async (regionMap) => {
    const promises = Object.entries(regionMap).map(async ([region, total]) => {
      const totalNum = Number(total) || 0;
      const existing = (data || []).find(d => normalizeReg(d.Region || d.region) === region);
      if (existing) {
        const idKey = existing.afeliding_id !== undefined ? 'afeliding_id' : (existing.afdeling_id !== undefined ? 'afdeling_id' : 'id');
        const { error } = await supabase
          .from('afdeling')
          .update({ Total: totalNum })
          .eq(idKey, existing[idKey]);
        if (error) throw error;
      } else {
        const { error } = await supabase
          .from('afdeling')
          .insert([{ Region: region, Total: totalNum }]);
        if (error) throw error;
      }
    });

    await Promise.all(promises);
    await fetchAfdeling();
  };

  return { data, loading, error, updateAfdeling, refetch: fetchAfdeling };
}
