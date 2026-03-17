export default defineEventHandler(async (event) => {
  // Temporary: use first user or create one if none exists for testing
  let user = await prisma.user.findFirst();
  if (!user) {
    user = await prisma.user.create({
      data: {
        email: 'test@example.com',
        name: 'Test User',
        password: 'password'
      }
    });
  }

  return await prisma.watchList.findMany({
    where: { userId: user.id },
    include: {
      movies: true,
      series: true
    }
  });
});
