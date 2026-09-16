import React from 'react';
import { createRoot } from 'react-dom/client';
import MonitoringAlumniTab from './MonitoringAlumniTab.jsx';

const MonitoringAlumniPage = () => {
    return <MonitoringAlumniTab />;
};

export default MonitoringAlumniPage;

const container = document.getElementById('monitoring-alumni-root');
if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <MonitoringAlumniPage />
        </React.StrictMode>
    );
}
