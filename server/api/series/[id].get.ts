export default defineEventHandler(async (event) => {
  const id = event.context.params?.id;
  if (!id) throw createError({ statusCode: 400, message: 'Missing ID' });

  const series = await prisma.series.findUnique({
    where: { id: parseInt(id) },
    include: {
      seasons: {
        include: {
          episodes: true
        }
      }
    }
  });

  if (!series) throw createError({ statusCode: 404, message: 'Series not found' });

  return {
    ...series,
    description: series.resume,
    rating: series.popularity,
    seasons: series.seasons.length
  };
});
