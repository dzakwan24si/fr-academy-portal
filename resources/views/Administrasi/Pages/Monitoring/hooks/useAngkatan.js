import { useState, useEffect, useRef } from 'react';
import { supabase } from '../lib/supabase';

export function useAngkatan() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchAngkatan = async () => {
    setLoading(true);
    setError(null);
    try {
      const { data: angkatanData, error: angkatanError } = await supabase
        .from('angkatan')
        .select('*')
        .order('id_angkatan', { ascending: false });

      if (angkatanError) throw angkatanError;

      const toNumber = (value) => {
        const parsed = Number(value ?? 0);
        return Number.isFinite(parsed) && parsed >= 0 ? parsed : 0;
      };

      const aggregatedData = (angkatanData || []).map(item => {
        const jumlahAwal = toNumber(item['Jumlah Awal']);
        const resignInClass = toNumber(item.Resign_In_Class ?? item.resign_in_class ?? item.resignInClass ?? 0);
        const resignOjt = toNumber(item.Resign_OJT ?? item.resign_ojt ?? item.resignOJT ?? 0);
        const tidakLulus = toNumber(item.tidak_lulus ?? item.tidakLulus ?? item['Tidak Lulus'] ?? 0);
        const totalLulus = Math.max(jumlahAwal - resignInClass - resignOjt - tidakLulus, 0);

        return {
          ...item,
          lulus_calculated: totalLulus,
          culled_calculated: resignInClass + resignOjt,
          tidak_lulus_calculated: tidakLulus,
          resign_in_class_calculated: resignInClass,
          resign_ojt_calculated: resignOjt
        };
      });

      setData(aggregatedData);

    } catch (err) {
      console.error("Error fetching angkatan:", err.message);
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const hasFetched = useRef(false);

  useEffect(() => {
    if (!hasFetched.current) {
      fetchAngkatan();
      hasFetched.current = true;
    }
  }, []);

  const addAngkatan = async (newAngkatan) => {
    let nextId = 1;
    if (data && data.length > 0) {
      nextId = Math.max(...data.map(item => item.id_angkatan || 0)) + 1;
    }

    const jumlahAwal = Number(newAngkatan['Jumlah Awal'] ?? 0) || 0;
    const resignInClass = Number(newAngkatan.Resign_In_Class ?? 0) || 0;
    const resignOjt = Number(newAngkatan.Resign_OJT ?? 0) || 0;
    const tidakLulus = Number(newAngkatan.tidak_lulus ?? 0) || 0;

    const { data: inserted, error } = await supabase
      .from('angkatan')
      .insert([{
        id_angkatan: nextId,
        angkatan: newAngkatan.angkatan,
        'Jumlah Awal': jumlahAwal,
        Resign_In_Class: resignInClass,
        Resign_OJT: resignOjt,
        tidak_lulus: tidakLulus,
        lulus: Math.max(jumlahAwal - resignInClass - resignOjt - tidakLulus, 0)
      }])
      .select();

    if (error) throw error;

    const formatted = {
      ...inserted[0],
      lulus_calculated: Math.max((Number(inserted[0]['Jumlah Awal']) || 0) - (Number(inserted[0].Resign_In_Class) || 0) - (Number(inserted[0].Resign_OJT) || 0) - (Number(inserted[0].tidak_lulus) || 0), 0),
      culled_calculated: (Number(inserted[0].Resign_In_Class) || 0) + (Number(inserted[0].Resign_OJT) || 0),
      tidak_lulus_calculated: Number(inserted[0].tidak_lulus) || 0,
      resign_in_class_calculated: Number(inserted[0].Resign_In_Class) || 0,
      resign_ojt_calculated: Number(inserted[0].Resign_OJT) || 0
    };
    setData([formatted, ...data]);
    return formatted;
  };

  const updateAngkatan = async (id, updatedAngkatan) => {
    const jumlahAwal = Number(updatedAngkatan['Jumlah Awal'] ?? 0) || 0;
    const resignInClass = Number(updatedAngkatan.Resign_In_Class ?? 0) || 0;
    const resignOjt = Number(updatedAngkatan.Resign_OJT ?? 0) || 0;
    const tidakLulus = Number(updatedAngkatan.tidak_lulus ?? 0) || 0;

    const { data: updated, error } = await supabase
      .from('angkatan')
      .update({
        angkatan: updatedAngkatan.angkatan,
        'Jumlah Awal': jumlahAwal,
        Resign_In_Class: resignInClass,
        Resign_OJT: resignOjt,
        tidak_lulus: tidakLulus,
        lulus: Math.max(jumlahAwal - resignInClass - resignOjt - tidakLulus, 0)
      })
      .eq('id_angkatan', id)
      .select();

    if (error) throw error;

    setData(data.map(item => {
      if(item.id_angkatan === id) {
        const nextLulus = Math.max(jumlahAwal - resignInClass - resignOjt - tidakLulus, 0);
        return {
          ...updated[0],
          lulus_calculated: nextLulus,
          culled_calculated: resignInClass + resignOjt,
          tidak_lulus_calculated: tidakLulus,
          resign_in_class_calculated: resignInClass,
          resign_ojt_calculated: resignOjt
        };
      }
      return item;
    }));
    return updated[0];
  };

  const deleteAngkatan = async (id) => {
    const { error } = await supabase
      .from('angkatan')
      .delete()
      .eq('id_angkatan', id);

    if (error) throw error;
    setData(data.filter(item => item.id_angkatan !== id));
  };

  return { 
    data, 
    loading, 
    error, 
    addAngkatan, 
    updateAngkatan, 
    deleteAngkatan, 
    refetch: fetchAngkatan 
  };
}
