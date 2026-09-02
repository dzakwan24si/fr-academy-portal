import { BrowserRouter, Routes, Route } from 'react-router-dom';
import AdminLayout from '../layouts/AdminLayout';
import Dashboard from './Dashboard';
import TrainingCalendar from './TrainingCalendar';
import DataManagement from './DataManagement';
import Schedule from './Schedule';

const getBasename = () => {
  const path = window.location.pathname;
  if (path.startsWith('/Top_Management')) return '/Top_Management';
  if (path.startsWith('/top_management')) return '/top_management';
  return '/top-management';
};

function App() {
  return (
    <BrowserRouter basename={getBasename()}>
      <Routes>
        <Route path="/" element={<AdminLayout />}>
          <Route index element={<Dashboard />} />
          <Route path="calendar" element={<TrainingCalendar />} />
          <Route path="schedule" element={<Schedule />} />
          <Route path="data" element={<DataManagement />} />
          <Route path="settings" element={<div className="p-6">Settings Page Placeholder</div>} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
