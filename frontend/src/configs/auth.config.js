/**
 * AICMS Laravel API Configuration
 * Update this URL to match your Laravel backend
**/

// Use environment variable or fallback to localhost
export const JWT_HOST_API = import.meta.env.VITE_API_URL || "http://localhost:8000/api/v1";
