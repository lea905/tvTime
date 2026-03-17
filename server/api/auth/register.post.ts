import bcrypt from 'bcryptjs';

export default defineEventHandler(async (event) => {
  const body = await readBody(event);
  const { email, password, name } = body;

  if (!email || !password || !name) {
    throw createError({ statusCode: 400, message: 'Tous les champs sont requis' });
  }

  const existingUser = await prisma.user.findUnique({ where: { email } });
  if (existingUser) {
    throw createError({ statusCode: 400, message: 'Cet email est déjà utilisé' });
  }

  const hashedPassword = await bcrypt.hash(password, 10);

  const user = await prisma.user.create({
    data: {
      email,
      name,
      password: hashedPassword
    }
  });

  // Automatically log in after registration
  setCookie(event, 'auth_user', JSON.stringify({ id: user.id, name: user.name, email: user.email }), {
    httpOnly: true,
    maxAge: 60 * 60 * 24 * 7 // 1 week
  });

  return { user: { id: user.id, name: user.name, email: user.email } };
});
