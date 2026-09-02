import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.VITE_MONITORING_SUPABASE_URL || import.meta.env.VITE_SUPABASE_URL || 'https://dhqdnvuvtraugifpwlpc.supabase.co';
const supabaseAnonKey = import.meta.env.VITE_MONITORING_SUPABASE_ANON_KEY || import.meta.env.VITE_SUPABASE_ANON_KEY || 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImRocWRudnV2dHJhdWdpZnB3bHBjIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODc4NjcxODQsImV4cCI6MjEwMzQ0MzE4NH0.ZPznTg47YLu9atU2wTJbIzWlKC4Q2eUQJdJ50Yr1u3I';

export const supabase = createClient(supabaseUrl, supabaseAnonKey);
