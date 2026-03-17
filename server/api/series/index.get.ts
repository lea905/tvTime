import { TMDB_GENRES } from '../../utils/tmdb';

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const selectedGenre = (query.genre as string) || Object.values(TMDB_GENRES)[0];
  
  const [popular, byGenre] = await Promise.all([
    prisma.series.findMany({
      orderBy: { popularity: 'desc' },
      take: 20
    }),
    prisma.series.findMany({
      where: {
        genres: {
          array_contains: selectedGenre
        }
      },
      orderBy: { popularity: 'desc' },
      take: 20
    })
  ]);

  const genres = Object.values(TMDB_GENRES);

  return {
    popular,
    byGenre,
    genres,
    selectedGenre
  };
});
