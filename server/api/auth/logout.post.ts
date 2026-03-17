export default defineEventHandler(async (event) => {
  deleteCookie(event, 'auth_user');
  return { message: 'Déconnecté' };
});
