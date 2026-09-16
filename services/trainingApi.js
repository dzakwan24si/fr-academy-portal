const normalize = (item) => ({
  ...item,
  start: item.start ? new Date(item.start) : null,
  end: item.end ? new Date(item.end) : null,
  tanggal_pelaksanaan: Array.isArray(item.tanggal_pelaksanaan)
    ? item.tanggal_pelaksanaan.map((date) => new Date(date))
    : [],
});

export const getAllPelatihan = async () => {
  try {
    const response = await fetch('/administrasi/program-batch/data', { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error('Data program tidak dapat dimuat.');
    const data = await response.json();
    return { data: data.map(normalize), error: null };
  } catch (error) {
    return { data: null, error };
  }
};

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

const request = async (url, method = 'POST', body = null) => {
  const response = await fetch(url, {
    method,
    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf() },
    body: body ? JSON.stringify(body) : undefined,
  });
  const result = await response.json().catch(() => ({}));
  return response.ok ? { data: result, error: null } : { data: null, error: new Error(result.message || 'Permintaan gagal.') };
};

export const addPelatihanNonReguler = async (data) => request('/administrasi/program-batch/non_reguler', 'POST', data);
export const addPelatihanReguler = async (data) => request('/administrasi/program-batch/reguler', 'POST', data);
export const updatePelatihanNonReguler = async (id, data) => request(`/administrasi/program-batch/non_reguler/${id}`, 'PUT', data);
export const updatePelatihanReguler = async (id, data) => request(`/administrasi/program-batch/reguler/${id}`, 'PUT', data);
export const deletePelatihanNonReguler = async (id) => request(`/administrasi/program-batch/non_reguler/${id}`, 'DELETE');
export const deletePelatihanReguler = async (id) => request(`/administrasi/program-batch/reguler/${id}`, 'DELETE');
export const deleteAllPelatihan = async (type = null) => request('/administrasi/program-batch/destroy-all', 'DELETE', { type });
