export default defineEventHandler(async (event) => {
  const query = getQuery(event);
  const type = query.type as string || 'movie'; // 'movie' or 'tv'
  const pages = 5; // Reduced for testing, can be increased
  const results = [];

  for (let page = 1; page <= pages; page++) {
    const data: any = await fetchFromTmdb(`/discover/${type}`, { page });
    
    for (const item of data.results) {
      if (type === 'movie') {
        const movie = await prisma.movie.upsert({
          where: { tmdbId: item.id },
          update: {
            title: item.title,
            picture: item.poster_path,
            resume: item.overview,
            popularity: item.popularity,
            releaseDate: item.release_date ? new Date(item.release_date) : null,
            genres: getGenreNames(item.genre_ids),
          },
          create: {
            tmdbId: item.id,
            title: item.title,
            picture: item.poster_path,
            resume: item.overview,
            popularity: item.popularity,
            releaseDate: item.release_date ? new Date(item.release_date) : null,
            genres: getGenreNames(item.genre_ids),
          },
        });
        results.push(movie);
      } else {
        const series = await prisma.series.upsert({
          where: { tmdbId: item.id },
          update: {
            title: item.name,
            picture: item.poster_path,
            resume: item.overview,
            popularity: item.popularity,
            releaseDate: item.first_air_date ? new Date(item.first_air_date) : null,
            genres: getGenreNames(item.genre_ids),
            // seasons/episodes would require extra calls per series
          },
          create: {
            tmdbId: item.id,
            title: item.name,
            picture: item.poster_path,
            resume: item.overview,
            popularity: item.popularity,
            releaseDate: item.first_air_date ? new Date(item.first_air_date) : null,
            genres: getGenreNames(item.genre_ids),
          },
        });
        results.push(series);
      }
    }
  }

  return {
    message: `${results.length} ${type === 'movie' ? 'films' : 'séries'} synchronisés`,
    count: results.length
  };
});
