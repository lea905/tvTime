export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const q = query.q as string;
  
  if (!q) return { movies: [], series: [] };

  const [movies, series] = await Promise.all([
    prisma.movie.findMany({
      where: {
        OR: [
          { title: { contains: q } },
          { resume: { contains: q } }
        ]
      },
      take: 20
    }),
    prisma.series.findMany({
      where: {
        OR: [
          { title: { contains: q } },
          { resume: { contains: q } }
        ]
      },
      take: 20
    })
  ]);

  return {
    movies,
    series
  };
});
