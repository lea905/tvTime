export default defineEventHandler(async () => {
  const [movies, series] = await Promise.all([
    prisma.movie.findMany({
      orderBy: { popularity: 'desc' },
      take: 20
    }),
    prisma.series.findMany({
      orderBy: { popularity: 'desc' },
      take: 20
    })
  ]);

  return {
    movies,
    series
  };
});
