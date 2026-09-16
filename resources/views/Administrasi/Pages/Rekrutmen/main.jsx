import React from 'react';
import { createRoot } from 'react-dom/client';
import RekrutmenApp from './RekrutmenApp.jsx';

const container = document.getElementById('rekrutmen-root');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <RekrutmenApp />
        </React.StrictMode>
    );
}
