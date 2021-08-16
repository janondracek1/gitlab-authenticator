# GitLab AuthenticatorUsed libraries/technologies: - PHP 8.0 - nette/application   - and it's dependencies     - nette/di     - nette/bootstrap     - ... - guzzlehttp/guzzle - contributte/console ## ForewordI purposefully didn't use `nette/sandbox` as it is too heavy for this project and contains tons of unusable dependencies, thus I chose the path of building my own little project based on Nette and Symfony components.I am fully aware of libraries like [GitLabPHP](https://github.com/GitLabPHP/Client), but I chose to implement API endpoints on my own, because I wanted to avoid using libraries for literally everything. Not to mention this way helped me understand the structure of the API way better.I also strongly stuck with YAGNI principle, so I ended up implementing only the endpoints I needed in order to complete the task and also getters/attributes on Entities which were necessary.## How to runClone repositoryRun `composer install`Copy and rename `config/gitlab-config.neon-example` to `config/gitlab-config.neon` and fill in needed parameters.Run `php bin/console gitlab:import <group id>`If you encounter `cURL error 6: Could not resolve host: gitlab.example.com...` follow tutorial [here](https://docs.bolt.cm/4.0/howto/curl-ca-certificates#installing-automatically-converted-ca-certificates-from-mozilla-org).## Assignment (Czech)Potřebujeme nástroj, kterým budeme kontrolovat, že nikdo nepovolaný nemá přístup k našim skupinám a projektům v Gitlabu.Pro úsporu času vývoje nepotřebujeme nic s UI, stačí aby se nástroj spouštěl z příkazové řádky, kde dostane jako argument ID top-level skupiny a vypíše výsledek v lidsky čitelném formátu.Access token nebude předáván jako argument spouštěného příkazu, ale měl by být snadno vyměnitelný. Jak splnit tento požadavek je ponecháno na řešiteli.### Jak fungují Gitlab skupiny, projekty a uživatelé- skupiny mohou být do sebe libovolně vnořovány, není omezena hloubka- projekty jsou vždy v nějaké skupině, nic jako sub-projekty neexistuje- uživatelé mohou být členy skupin i projektů### Potřebná data ve výstupu- výstup by měl být seznam uživatelů s detaily (viz další bod)- k uživatelům potřebujeme vědět    - občanské jméno (tzn. jméno a příjmení)    - uživatelské jméno    - seznam skupin jejichž je členem a s jakým oprávněním    - seznam projektů jejichž je členem a s jakým oprávněním- na konci výstupu by měl být celkový počet uživatelů### Ukázkový výstupNa testovacím prostředí by měl nástroj dát tato data. Formát není nutné zachovat, ale chceme, aby byl nástroj reálně použitelný, tedy měl by být podobně čitelný.```Jan Konáš (@jan.konas)Groups:    [apploud/backend-testovaci-zadani (Owner)]Projects:  []Jan Konáš (@jankonas1)Groups:    [apploud/backend-testovaci-zadani (Owner)]Projects:  []Michal Pham (@KhanhPhams)Groups:    [apploud/backend-testovaci-zadani/skupina-3 (Guest)]Projects:  [apploud/backend-testovaci-zadani/uloha-1 (Guest)]Martin Špicar (@martin.spicar)Groups:    []Projects:  [apploud/backend-testovaci-zadani/uloha-1 (Developer), apploud/backend-testovaci-zadani/skupina-2/skupina-4/projekt-3 (Guest), apploud/backend-testovaci-zadani/skupina-3/projekt-2 (Guest)]Michal Bílý (@MichalBily)Groups:    [apploud/backend-testovaci-zadani/skupina-1 (Guest)]Projects:  []Total Users: 5```### ŠkálovatelnostTestovací prostředí má 5 uživatelů, 5 skupin (včetně top-level) a 4 projekty. Nástroj ale musí fungovat i na reálném prostředí, které má okolo 500 projektů, nižší desítky skupin a kolem 50 uživatelů.## Přístupové údaje a další informace k vývojiPro vývoj je k dispozici read-only testovací prostředí s těmito údaji:- ID top-level skupiny: `10975505`- Access token: `naRAbrD8qPXaXVASQ8Zy`Je potřeba využít Gitlab REST API, nikoli GraphQL. Jeho dokumentace je zde: https://docs.gitlab.com/ee/api/