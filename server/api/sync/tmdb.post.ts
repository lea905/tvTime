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

        try {
          const seriesDetails: any = await fetchFromTmdb(`/tv/${item.id}`);
          
          if (seriesDetails && seriesDetails.number_of_episodes !== undefined) {
             await prisma.series.update({
               where: { id: series.id },
               data: {
                 numberEpisodes: seriesDetails.number_of_episodes,
                 numberSeasons: seriesDetails.number_of_seasons,
                 status: seriesDetails.status
               }
             });
          }

          if (seriesDetails && seriesDetails.seasons) {
            for (const seasonData of seriesDetails.seasons) {
              if (seasonData.season_number === 0) continue; // Skip specials

              const season = await prisma.season.upsert({
                where: { tmdbId: seasonData.id },
                update: {
                  number: seasonData.season_number,
                  title: seasonData.name,
                  picture: seasonData.poster_path,
                  numberEpisodes: seasonData.episode_count,
                  resume: seasonData.overview,
                  seriesId: series.id
                },
                create: {
                  tmdbId: seasonData.id,
                  number: seasonData.season_number,
                  title: seasonData.name,
                  picture: seasonData.poster_path,
                  numberEpisodes: seasonData.episode_count,
                  resume: seasonData.overview,
                  seriesId: series.id
                }
              });

              try {
                const seasonDetails: any = await fetchFromTmdb(`/tv/${item.id}/season/${seasonData.season_number}`);
                if (seasonDetails && seasonDetails.episodes) {
                  for (const epData of seasonDetails.episodes) {
                    await prisma.episode.upsert({
                      where: { tmdbId: epData.id },
                      update: {
                        number: epData.episode_number,
                        title: epData.name,
                        resume: epData.overview,
                        releaseDate: epData.air_date ? new Date(epData.air_date) : null,
                        seasonId: season.id
                      },
                      create: {
                        tmdbId: epData.id,
                        number: epData.episode_number,
                        title: epData.name,
                        resume: epData.overview,
                        releaseDate: epData.air_date ? new Date(epData.air_date) : null,
                        seasonId: season.id
                      }
                    });
                  }
                }
              } catch (e) {
                console.error(`Error fetching episodes for series ${item.id} season ${seasonData.season_number}`, e);
              }
            }
          }
        } catch (e) {
          console.error(`Error fetching details for series ${item.id}`, e);
        }

        results.push(series);
      }
    }
  }

  return {
    message: `${results.length} ${type === 'movie' ? 'films' : 'séries'} synchronisés`,
    count: results.length
  };
});
