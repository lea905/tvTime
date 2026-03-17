import bcrypt from 'bcryptjs';

export default defineEventHandler(async (event) => {
  const body = await readBody(event);
  const { email, password } = body;

  if (!email || !password) {
    throw createError({ statusCode: 400, message: 'Email et mot de passe requis' });
  }

  const user = await prisma.user.findUnique({ where: { email } });
  if (!user || !(await bcrypt.compare(password, user.password))) {
    throw createError({ statusCode: 401, message: 'Identifiants invalides' });
  }

  setCookie(event, 'auth_user', JSON.stringify({ id: user.id, name: user.name, email: user.email }), {
    httpOnly: true,
    maxAge: 60 * 60 * 24 * 7 // 1 week
  });

  return { user: { id: user.id, name: user.name, email: user.email } };
});
