# Zip-codes API Code Challenge - Laravel Backend Developer
In this project, we are creating an API endpoint that returns data about any zip-codes in Mexico.

## Analysis 
The first step was to analyze the provided data set for zip-codes in Mexico ([Zip-codes](https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx))


After downloading the provided data set from ([Zip-codes](https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx)), a comparison was made with the fields shown in the provided ([API](https://jobs.backbonesystems.io/api/zip-codes/01210)).

It was immediately apperant that the data presented in the API response is structured in a more coherent and logically related manner, with fields that are meaningfully labeled and in English language.

Furthermore, the provided data set contains various relationships between different entities. For instance, the "zip_code" is related to the "locality", which is in turn related to the "municipality". The "municipality" is related to the "federal_entity", which has a "key", "name", and "code" associated with it. Additionally, the "settlements" entity is related to the "zip_code" entity and can have many different "settlement_type" values associated with it. This many-to-many relationship allows for multiple settlement types to be associated with a single settlement. Overall, the relationships within the data set are complex but well-defined, and will allow for efficient storage and retrieval of the relevant data.

With this in mind, I created a schema that would match this logic and serve as the foundation for the database.


## Database
In pgAdmin, I used the ERD Diagram tool, which is a canvas for creating Entity-Relationship Diagrams. Then, I declared the tables according to the comparison made with the example API, resulting in the following tables:

- Federal Entity
- Municipalities
- Zip Codes
- Settlements
- Selttement Types

![image](https://user-images.githubusercontent.com/45053439/213291435-81169c5c-1778-4943-8d21-2273be5d99c9.png)


## Laravel
After verifying that the structure of my database was correct, I proceeded to create the Laravel project by deleting all unnecessary routes such as web and channel. I uninstalled the default Laravel Sanctum to avoid creating unnecessary migrations and proceeded to install my environment using ([Sail](https://laravel.com/docs/10.x/sail#main-content)).


### Laravel Migrations
To set up the database schema for the project, I used Laravel Migrations. I created a new migration file for each of the tables I had designed in the Entity-Relationship Diagram (ERD).

For instance, the migration for the federal_entities table looks like this:
```php
Schema::create('federal_entities', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('key');
            $table->string('name');
            $table->string('code')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
```
and the migration for zip_codes looks as follows:
```php
Schema::create('zip_codes', function (Blueprint $table) {
            $table->id();
            $table->string('zip_code')->index();
            $table->string('locality')->nullable()->default(null);
            $table->foreignIdFor(FederalEntity::class)->constrained();
            $table->foreignIdFor(Municipality::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
```

I created an index for the zip_code column in the Zip Codes table to improve search performance for requests. Additionally, I added two foreign keys for its relationships with Federal Entities and Municipalities in a one-to-many relationship.

### Models
The models were straightforward to create after the migrations were set up. I simply used Laravel's command to generate the Model, Factory, and Seeder:

> php artisan make:model ZipCode -mfsc

The zip-code model was the most complicated of the bunch as it includes relationships with other models such as FederalEntity, Municipality, and Settlement. The "belongsTo" method is used to establish a one-to-many relationship between ZipCode and FederalEntity, Municipality. On the other hand, "belongsToMany" is used to establish a many-to-many relationship between ZipCode and Settlement. Additionally, there is a custom method called "resolveRouteBindingQuery" used to resolve the route binding.

```php
/**
     * Get the federal entity that owns the zip code.
     */
    public function federalEntity()
    {
        return $this->belongsTo(FederalEntity::class);
    }

    /**
     * Get the municipality that owns the zip code.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * The settlements that belong to the zip code.
     */
    public function settlements()
    {
        return $this->belongsToMany(Settlement::class)->orderBy('key');
    }
```


