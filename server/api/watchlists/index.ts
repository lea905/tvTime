// server/api/watchlists.ts
export default defineEventHandler(async (event) => {
  const user = await prisma.user.findFirst();

  if (!user) {
    throw createError({
      statusCode: 401,
      statusMessage: 'Unauthorized',
      message: 'Vous devez être connecté pour accéder aux listes.'
    });
  }

  const method = event.method;

  if (method === 'GET') {
    // Si on demande juste à voir les listes
    return await prisma.watchList.findMany({
      where: { userId: user.id },
      include: { movies: true, series: true }
    });
  }

  if (method === 'POST') {
    // Si on a cliqué sur le bouton "Nouvelle liste"
    const body = await readBody(event); // On récupère les données envoyées par le bouton

    return await prisma.watchList.create({
      data: {
        title: body.title || 'Ma nouvelle liste',
        userId: user.id,
      },
      include: {movies: true, series: true}
    });
  }
});