# Zip-codes API Code Challenge - Laravel Backend Developer
In this project, we are creating an API endpoint that returns data about any zip-code in Mexico.

## Analysis 
The first step was to analyze the provided data set for zip-codes in Mexico ([Data set](https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx))


After downloading the provided data set from ([Data set](https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx)), a comparison was made with the fields shown in the provided ([Example API](https://jobs.backbonesystems.io/api/zip-codes/01210)).

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

    > php artisan make:model ZipCode -mfsc
    
    // This creates the model along with the factory, seeder and controller

### Zip-code model

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

### Factories and Seeders
Next, I began working with Factories to populate a test and development database, following the stardard protocol for how factories are created.

<img width="621" alt="Screenshot 2023-01-18 at 13 08 42" src="https://user-images.githubusercontent.com/45053439/213295089-fca53c82-4519-4045-915d-160b92bf2627.png">

The seeders required a bit more work to be able to relate everything in order and at the same time generate multiple records in the database to test query speed. A global seeder was created where the necessary records are created.

<img width="915" alt="Screenshot 2023-01-18 at 13 10 03" src="https://user-images.githubusercontent.com/45053439/213295340-097e680c-3c4a-441d-8e63-9725ba9fea25.png">

Above we observe how the relationships between the different models are established using Laravel's for loop and the hasAttached method, which are important tools that allow for the creation of complex relationships between models. This approach is a practical and efficient way to populate the database with multiple records, while ensuring that all the relationships between them are properly established.

Factory data generated
- 31 Federal Entities
- 100 Municipalities
- 15 Settlement Types
- For every Federal Entity:
    - Created 1 to 99 Settlements
    - 200 Zip Codes
    
### Routes
I created two routes (although only one is used) for testing pagination and a regular get request for a single Zip Code. The routes were organized as follows:

Then I grouped them under the ZipCodeController, assigned the name zip-codes to use it later in the tests, and generated the index and show routes respectively. In the index function, I performed basic pagination, while in the show function, I used Route Binding to perform the query.

<img width="743" alt="Screenshot 2023-01-18 at 13 13 27" src="https://user-images.githubusercontent.com/45053439/213295963-0e7a9b1d-ace3-40cc-ab55-628151c35c7f.png">

## Resources

In the resource directory, I declared the relationships to include in my responses. I created a resource for each entity to format the endpoint in the best possible way. 

<img width="259" alt="Screenshot 2023-01-18 at 13 17 46" src="https://user-images.githubusercontent.com/45053439/213296758-2357420e-de45-4950-8398-08d785223ce2.png">

The Federal Entity, Municipality, and Settlement Type have nothing and are set to default with:

    > public function toArray($request) { return parent::toArray($request); }

But for the Zip codes and Settlements I did add the relationships.

<img width="891" alt="Screenshot 2023-01-18 at 13 20 22" src="https://user-images.githubusercontent.com/45053439/213297313-472a5315-5f6e-4dcb-8309-2f1394874558.png">

For Zipcodes I created the 3 important relationships:
- Municipality
- Federal Entity
- Settlements

### Controllers
In the ZipCodeController, I created two functions index and show to handle the corresponding routes. The index function implements basic pagination, while the show function utilizes the Route Binding feature to execute the query.

The Route Binding feature allows us to retrieve a model instance based on its identifier value. In the show function, the ZipCode model is retrieved based on the zip_code value passed as a parameter in the route. This eliminates the need for additional code to retrieve the model and handle errors in case of a failed retrieval.

Overall, these functions are structured to provide efficient and accurate retrieval of data for the corresponding routes.

<img width="654" alt="Screenshot 2023-01-18 at 13 16 43" src="https://user-images.githubusercontent.com/45053439/213296558-a6b06db1-cd96-4d18-bcfc-812c3fc1fe09.png">

Even though the responses generated by the endpoints were correct, they were missing the related entities because I was not executing the **Eager Loading or Lazy Loading** anywhere. To simplify the process, I overrode the Route Binding of the Zip Code model to achieve this.

By modifying the **resolveRouteBindingQuery** function, I was able to eager load the federalEntity, settlements, and municipality relations of the Zip Code model, allowing me to return a fully-formed response with all of the necessary data. This way, the response time of the application is improved, and the client receives a complete and organized set of data in one request.

<img width="987" alt="Screenshot 2023-01-18 at 13 25 01" src="https://user-images.githubusercontent.com/45053439/213298208-255bae35-0820-4087-8190-a596ef110bb8.png">

However, the Settlement Type relationship was not being displayed in the response. To solve this issue, I added the **$with** field to the Settlement model. This will load the Settlement Type relationship every time the Settlement model is retrieved, ensuring that it is included in the response.

<img width="510" alt="Screenshot 2023-01-18 at 13 28 55" src="https://user-images.githubusercontent.com/45053439/213298923-cc013a4d-b58b-4db5-b0bc-e195e717b421.png">

And with this the API response structure is complete.

<img width="354" alt="Screenshot 2023-01-18 at 13 29 48" src="https://user-images.githubusercontent.com/45053439/213299072-61ab7f52-24a1-4f8e-8fd7-a6d3b63f4eb5.png">

### Data Upload
The next part of the challenge was the data upload, which involved taking spreadsheets, grouping them into the new data structure, and then uploading them. Here's what I did:

1. CSV Download
    - First, I downloaded one CSV at a time for each Federal Entity and grouped them in the storage/app/files folder, naming them after the corresponding       entity.
    
2. Artisan Command
    - Next, I created an artisan command called upload:settlements to read the CSV and associate the data.
    - I grouped the columns into an array to make the matching process easier.
    - I installed the Laravel Excel package to simplify CSV reading.
    - I created the command with 3 parameters: entity name, key, and file path.
    
<img width="586" alt="Screenshot 2023-01-18 at 15 07 28" src="https://user-images.githubusercontent.com/45053439/213314873-3e662274-42a6-41bf-a590-fc2a3ee75470.png">

This allows us to run the following command to upload all Settlements for that Federal Entity: 

    > php artisan upload:settlements 'Aguascalientes' 01 app/files/Aguascalientes.csv 


3. Handler

    - First, we create the Federal Entity with the given parameters if it doesn't already exist.

    - <img width="706" alt="Screenshot 2023-01-18 at 15 09 23" src="https://user-images.githubusercontent.com/45053439/213315130-92179896-ab10-4522-bf95-019f6e65c3fc.png">

    - Then, we read the selected CSV file.

    - <img width="723" alt="Screenshot 2023-01-18 at 15 09 50" src="https://user-images.githubusercontent.com/45053439/213315184-a13d9483-9b5d-45a6-888a-66efb78e57c0.png">

    - We use the function `$this->withProgressBar($slice, function ($row) use ($federal_entity) {` to show a progress bar as the program uploads the data      from the csv
    
    - Then mapping the column names with each record in the CSV to make the use of each field more readable and not relying on indices. This is achieved        by creating an array to group the columns, making it easier to match them. 
 
    - <img width="473" alt="Screenshot 2023-01-18 at 15 10 28" src="https://user-images.githubusercontent.com/45053439/213315277-46a4ab30-995e-44ed-9f71-63985e5c64e3.png">

    - After mapping the column names to each record in the CSV for more legible usage of each field, we extract the municipality of the settlement and search for it or create it. 
    - Then, we search or create the zip code with its respective relationships to the municipality and federal entity created later.
    - Next, we search or create the settlement type. Finally, we search or create the settlement in question, attaching it to the zip code and settlement type created earlier. 
    - This process is repeated for each record in the CSV.

4. Bash
    - To avoid having to write a command for each Federal Entity individually, I created a bash file that includes all of them with their corresponding key and file path. This approach streamlines the process and allows for the efficient uploading of data. The bash file can be easily executed with the command bash upload-settlements.sh, simplifying the entire data upload process. This approach saves a significant amount of time and ensures that all Federal Entities are processed correctly and uniformly.

<img width="881" alt="Screenshot 2023-01-18 at 15 14 49" src="https://user-images.githubusercontent.com/45053439/213315934-d6e61a2b-c392-4966-9963-e6df76c91051.png">

This can be executed by simply running the command:

    >bash upload-settlements.sh
    
    
## Deployment



    
## Results

