export default defineEventHandler(async (event) => {
  const authCookie = getCookie(event, 'auth_user');
  if (!authCookie) return null;
  
  try {
    return JSON.parse(authCookie);
  } catch {
    return null;
  }
});
