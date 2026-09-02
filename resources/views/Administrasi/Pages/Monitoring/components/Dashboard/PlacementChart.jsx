import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Cell
} from "recharts";

export function PlacementChart({ data = [] }) {
  const placementData = data.reduce((acc, emp) => {
    if (emp.STATUS === "Eksis" || emp.STATUS === "Aktif") {
      const region = emp["REGION TERAKHIR"] || "Tidak diketahui";
      if (!acc[region]) {
        acc[region] = { name: region, Eksis: 0 };
      }
      acc[region].Eksis += 1;
    }
    return acc;
  }, {});

  const chartData = Object.values(placementData);
  const colors = ['#2c8f42', '#0a4239', '#5fd278'];

  return (
    <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
      <div className="mb-6">
        <h3 className="text-lg font-bold text-gray-800">Placement Distribution</h3>
        <p className="text-xs text-gray-400 mt-1">Sebaran karyawan aktif berdasarkan region</p>
      </div>
      
      <div className="flex-1 min-h-[300px] w-full">
        {chartData.length > 0 ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart
              layout="vertical"
              data={chartData}
              margin={{ top: 20, right: 20, left: 20, bottom: 5 }}
            >
              <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="#f0f0f0" />
              <XAxis type="number" axisLine={false} tickLine={false} tick={{fill: '#9ca3af', fontSize: 12}} dy={10} />
              <YAxis dataKey="name" type="category" axisLine={false} tickLine={false} tick={{fill: '#9ca3af', fontSize: 12}} dx={-10} />
              <Tooltip 
                cursor={{ fill: 'transparent' }}
                contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)' }}
              />
              <Bar dataKey="Eksis" name="Karyawan aktif" radius={[0, 10, 10, 0]} barSize={40}>
                {chartData.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={colors[index % colors.length]} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        ) : (
          <div className="flex h-full items-center justify-center text-gray-400">
            No placement data
          </div>
        )}
      </div>
    </div>
  );
}
