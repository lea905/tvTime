import { TMDB_GENRES } from '../../utils/tmdb';

export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const selectedGenreQuery = query.genre as string;
  const isTout = !selectedGenreQuery || selectedGenreQuery === 'Tout' || selectedGenreQuery === '';
  
  const genres = ['Tout', ...Object.values(TMDB_GENRES)];
  const selectedGenre = isTout ? 'Tout' : selectedGenreQuery;
  
  const [popular, byGenre] = await Promise.all([
    prisma.movie.findMany({
      orderBy: { popularity: 'desc' },
      take: 50
    }),
    isTout 
      ? prisma.movie.findMany({
          orderBy: { popularity: 'desc' }
        })
      : prisma.movie.findMany({
          where: {
            genres: {
              array_contains: selectedGenre
            }
          },
          orderBy: { popularity: 'desc' },
          take: 50
        })
  ]);

  return {
    popular,
    upcoming: popular,
    byGenre,
    genres,
    selectedGenre
  };
});
