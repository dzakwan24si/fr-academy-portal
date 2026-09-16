import React from 'react';
import { createRoot } from 'react-dom/client';
import MonitoringSummarizedTab from './MonitoringSummarizedTab.jsx';

const MonitoringSummarizedPage = () => {
    return <MonitoringSummarizedTab />;
};

export default MonitoringSummarizedPage;

const container = document.getElementById('monitoring-summarized-root');
if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <MonitoringSummarizedPage />
        </React.StrictMode>
    );
}
