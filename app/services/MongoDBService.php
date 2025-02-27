<?php

namespace App\Services;

use OpenAI;

class MongoDBService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function generatePipeline($query)
    {
        // Define the system prompt to guide OpenAI in generating MongoDB pipeline queries
        // $collectionName = 'users';
        // $schema = 'runtime(numeric)';
        // $prompt ="Given the following MongoDB schema for the '$collectionName' collection: $schema". 
        // "Generate a MongoDB aggregation pipeline in JSON format.The regex should perform a partial match, so do not include `^` at the beginning or `$` at the end of the pattern. The pipeline should filter or sort results and the sort before filter based on the given query: \"$query\".".
        // "and limit of 3.".
        // "and Ensure the following rules are strictly followed:
        // don not use any paramter outside the schema info $schema
        // respect the data type of schema paramters
        // ✅ Only use numeric values for numeric fields.
        // ✅ Convert date-related values into Unix timestamps (seconds).
        // ✅ Ignore conditions that try to filter a numeric field using a string.";

        //$translationPrompt = "Translate the following query into a clear and precise English question without changing its meaning:\n\n$query\n\nReturn only the translated question, nothing else.";
        // $translationPrompt = "Translate the following query into a clear and precise English without changing its meaning:\n\n$query\n\nReturn only the translated query, nothing else.";

        // $translationResponse = $this->client->chat()->create([
        //     'model' => 'gpt-4o',
        //     'messages' => [
        //         ['role' => 'system', 'content' => 'You are a language expert. Translate user queries into precise English questions.'],
        //         ['role' => 'user', 'content' => $translationPrompt],
        //     ],
        //     'temperature' => 0.2,
        // ]);

        // $translationPrompt = "You are a real estate expert in u. Translate the following real estate-related query into clear and precise English without changing its meaning:\n\n$query\n\nReturn only the translated query, nothing else.";

        // $translationResponse = $this->client->chat()->create([
        //     'model' => 'gpt-4o',
        //     'messages' => [
        //         ['role' => 'system', 'content' => 'You are a real estate expert. Translate user queries into precise English while maintaining their original meaning.'],
        //         ['role' => 'user', 'content' => $translationPrompt],
        //     ],
        //     'temperature' => 0.2,
        // ]);

        $translationPrompt = "You are a real estate expert specializing in the United Arab Emirates. Translate the following real estate-related query into clear and precise English without changing its meaning:\n\n$query\n\nReturn only the translated query, nothing else.";

        $translationResponse = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a real estate expert based in the United Arab Emirates. Translate user queries into precise English while maintaining their original meaning and considering local real estate market terminology.'],
                ['role' => 'user', 'content' => $translationPrompt],
            ],
            'temperature' => 0.2,
        ]);

        $translatedQuestion = $translationResponse['choices'][0]['message']['content']; // Extract translated question


        //     $collectionName = 'realestate';
        //     $schema = 'available_units(numeric),project_name(string),price_per_sqm(numeric)';

        //     $prompt = "Given the following MongoDB schema for the '$collectionName' collection: $schema, generate a MongoDB aggregation pipeline in JSON format.

        //     ### **Instructions (Strictly Follow)**
        //     ✅ **Sort and/or filter only if necessary** based on the given query: \"$translatedQuestion\".  
        //     ✅ **If no sorting is needed, exclude the sort stage**.  
        //     ✅ **If no filtering is needed, exclude the match stage**. 
        //     ✅ **Limit the results to 5**. 
        //     ✅ **you can use multible filters**. 
        //     ✅ **Use only fields specified in the schema** ($schema).  
        //     ✅ **Respect data types** (e.g., numeric fields must have numeric values).  
        //     ✅ **Use regex for partial string matching**, but **do NOT** include `^` at the beginning or `$` at the end of the pattern.  
        //     ✅ **Convert date-related values into Unix timestamps (seconds).**  
        //     ✅ **Ignore conditions that attempt to filter a numeric field using a string.**
        //     ✅ **Do not select only the required fields** 
        //     ✅ **Exclude `null` values from the sorting process**.
        //    **Ensure that fields used in any mathematical operations (such as \"\$multiply\") are numeric**. Use \"\$toDouble\" or \"\$toInt\" for converting fields like `price_per_sqm` or `available_units` to numeric types before performing any mathematical operations.
        //       Return only a valid JSON array, nothing else.";
        // $exclude_null = "**Exclude `null` values from the sorting or filtering process**. Ensure that fields like `available_units`, `price_per_sqm`, or
        //  `project_name` do not include `null` values when they are used in sorting or filtering.";



        //     $collectionName = 'realestate';
        //     $schema = 'project_name(string),project_type(string),total_units(numeric),available_units(numeric),launch_date(numeric),completion_date(numeric),project_size(string),area_name(string),region(string),dld_area_name(string),developer_name(string),table_name(string),starting_price_range(numeric),min_price_range_SQ(numeric),project_size_sqmt(numeric)';  

        //     $prompt = "Given the following MongoDB schema for the '$collectionName' collection: $schema, generate a MongoDB aggregation pipeline in JSON format.

        //     ### **Instructions (Strictly Follow)**
        //     ✅ **Sort and/or filter only if necessary** based on the given query: \"$translatedQuestion\".  
        //     ✅ **If no sorting is needed, exclude the sort stage**.  
        //     ✅ **If no filtering is needed, exclude the match stage**. 
        //     ✅ **Limit the results to 5**. 
        //     ✅ **you can use multible filters**. 
        //     ✅ **Use only fields specified in the schema**.  
        //     ✅ **Respect data types** (e.g., numeric fields must have numeric values).  
        //     ✅ **Use regex for partial string matching**, but **do NOT** include `^` at the beginning or `$` at the end of the pattern.  
        //     ✅ **Convert date-related values into Unix timestamps (seconds).**  
        //     ✅ **Ignore conditions that attempt to filter a numeric field using a string.**
        //     ✅ **Do not select only the required fields** 
        //     ✅ **Exclude `null` values from the sorting process**.
        //    **Ensure that fields used in any mathematical operations (such as \"\$multiply\") are numeric**. Use \"\$toDouble\" or \"\$toInt\" for converting fields like `price_per_sqm` or `available_units` to numeric types before performing any mathematical operations.
        //       Return only a valid JSON array, nothing else.";
        // $schema = 'project_name(string),project_type(string) may be residental or commercial,
        // total_units(numeric),available_units(numeric),launch_date(numeric),completion_date(numeric),
        // project_size(string),area_name(string),dld_area_name(string),developer_name(string),
        // table_name(string) must be projects or properties,starting_price_range(numeric),min_price_range_SQ(numeric),project_size_sqmt(numeric),
        // landmark(string),property_size(numeric),property_price(numeric)';  
        //             $collectionName = 'realestate';


        //         $schema = "The MongoDB collection '$collectionName' contains data related to real estate projects and properties.  
        // Schema details are as follows:Projects Table (`projects`): project_name (string), project_type (string) [Allowed values: 'residential', 'commercial'], total_units (numeric), available_units (numeric), project_launch_date (numeric) [Timestamp], project_completion_date (numeric) [Timestamp], project_size (string), area_name (string), dld_area_name (string), developer_name (string), starting_price_range (numeric), min_price_range_SQ (numeric), project_size_sqmt (numeric).
        // Properties Table (`properties`): property_size (numeric), property_price (numeric), project_name (string), landmark (string).
        // Ensure that any queries or operations correctly distinguish between `projects` and `properties` based on the relevant fields. ";

        //         $prompt = "Given the following $schema  
        // MongoDB Aggregation Pipeline Instructions: Generate a valid JSON aggregation pipeline based on User Query: `$translatedQuestion`.
        // Rules for Query Construction: Filtering & Sorting: Include \$match and \$sort only if needed (ignore `null` values in sorting). Limit: Always return 3 results. Filtering Logic: Use multiple filters if applicable. Ensure numeric fields are compared numerically. Use regex for strings but avoid `^` and `$` anchors. Ignore invalid type comparisons (e.g., string filtering on numeric fields). Mathematical Operations: Apply numeric operations (\$multiply, etc.) only on numeric fields. Field Selection: Return all fields unless specified otherwise. Output Restrictions: Return only a valid JSON array (no extra text). Return `{}` if no matching data exists.";
        // $collectionName = 'realestate_ai_test';

        // $schema = "MongoDB collection '$collectionName' contains data related to real estate projects and properties. "
        //     . "Schema details are as follows: Projects Table (`projects`):table_name(string), project_name (string), "
        //     . "total_units (numeric), available_units (numeric), "
        //     . "project_size (string), area_name (string), dld_area_name (string), developer_name (string), starting_price_range (numeric), "
        //     . "min_price_range_SQ (numeric), project_size_sqmt (numeric) ,landmark (string).project_launch_date (numeric) [Timestamp], project_completion_date (numeric) [Timestamp], "
        //     . "Properties Table (`properties`): table_name(string), property_size (numeric), property_price (numeric), project_name (string). "
        //     . "Ensure that any queries or operations correctly distinguish between `projects` and `properties` based on the relevant fields.";

        // $prompt = "Given the following $schema "
        //     . "MongoDB Aggregation Pipeline Instructions: Generate a valid JSON aggregation pipeline based on User Query: `$translatedQuestion`. "
        //     . "Rules for Query Construction: Filtering & Sorting: Include \$match and \$sort only if needed (ignore `null` values in sorting). "
        //     . "Filtering Logic: Use multiple filters if applicable. Ensure numeric fields are compared numerically. "
        //     . "Use regex for strings but avoid `^` and `$` anchors. Ignore invalid type comparisons (e.g., string filtering on numeric fields). "
        //     . "Use \$sort and \$match stages only and do not use mathmatical operations like \$multiply and \$add. Field Selection: Return all fields unless specified otherwise. "
        //     ."Convert **date-related values** (`project_launch_date`, `project_completion_date`) into **Unix timestamps (seconds)"
        //     . "Output Restrictions: The output **must be a valid JSON array** containing the aggregation pipeline(no extra text). Return `{}` if no matching data exists.";

        // // Remove extra spaces, newlines, and tabs
        // $prompt = preg_replace('/\s+/', ' ', $prompt);

        $collectionName = 'realestate_ai_test';

        $schema = "The MongoDB collection '$collectionName' contains data related to real estate projects and properties. "
            . "Schema details are as follows: Projects Table (`projects`): project_name (string), project_type (string) [Allowed values: 'residential', 'commercial'], "
            . "total_units (numeric), available_units (numeric), landmark (string),"
            . "project_size (string), area_name (string), dld_area_name (string), developer_name (string), starting_price_range (numeric), "
            . "min_price_range_SQ (numeric), project_size_sqmt (numeric). "
            . "Properties Table (`properties`): property_size (numeric), property_price (numeric), project_name (string), landmark (string). "
            . "Ensure that any queries or operations correctly distinguish between `projects` and `properties` based on the relevant fields. "
            . "All date fields (`launch_date`, `completion_date`) must be treated as Unix timestamps (seconds).";

        $prompt = "Given the following $schema "
            . "MongoDB Aggregation Pipeline Instructions: Generate a valid JSON aggregation pipeline based on User Query: `$translatedQuestion`. "
            . "Rules for Query Construction: Filtering & Sorting: Include only the **\$match** and **\$sort** stages when applicable. "
            . "Ignore `null` values in sorting. **Do not include \$addFields, \$multiply,\$add or any computed fields, even if required by the query.** "
            . "Limit: Always return 3 results. Filtering Logic: Use multiple filters if applicable. Ensure numeric fields are compared numerically. "
            . "Use regex for strings but avoid `^` and `$` anchors. Ignore invalid type comparisons (e.g., string filtering on numeric fields). "
            //. "Date Handling: Always treat `project_launch_date` and `project_completion_date` as Unix timestamps (seconds) in any date-related operations. "
            . "Field Selection: Return all fields unless specified otherwise. "
            . "Output Restrictions: Return only a valid JSON array (no extra text). Return `{}` if no matching data exists.";

        // Remove extra spaces, newlines, and tabs
        $prompt = preg_replace('/\s+/', ' ', $prompt);


        // Call OpenAI API
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o', // Use GPT-4 for better structured responses
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert MongoDB query builder.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3, // Lower temperature for structured output
        ]);

        // Extract the generated content
        $generatedPipeline = $response->choices[0]->message->content;

        return [
            'prompt' => $prompt,
            'translated_question' => $translatedQuestion,
            'pipeline' => $generatedPipeline
        ];
    }


    // $prompt = "Given the following MongoDB schema for the '$collectionName' collection:  
    //     **Schema:** $schema  

    //     ### **Strict Query Generation Instructions**
    //     Your task is to generate an accurate **MongoDB aggregation pipeline in JSON format** based on the given user query:  
    //     ➡️ **User Query:** \"$translatedQuery\"  

    //     ### **Rules to Follow for Query Construction**
    //     ✅ **Filtering & Sorting (Only When Needed):**  
    //     - If the query requires filtering, include a **\$match** stage; otherwise, exclude it.  
    //     - If sorting is required, include a **\$sort** stage. **Exclude it if unnecessary.**  
    //     - Ensure sorting ignores `null` values.  

    //     ✅ **Limit the Results:**  
    //     - Always limit the output to **3 results**.  

    //     ✅ **Filtering Logic:**  
    //     - Use **multiple filters** when applicable.  
    //     - **Respect Data Types** → Ensure numeric fields are compared numerically.  
    //     - **Use Regex  for Partial String Matching**, but avoid `^` and `$` anchors to ensure flexible searches.  
    //     - **Ignore queries that attempt to filter a numeric field using a string.**  

    //     ✅ **Handling Dates:**  
    //     - Convert **date-related values** (`launch_date`, `completion_date`) into **Unix timestamps (seconds)** when processing.  

    //     ✅ **Data Integrity & Mathematical Operations:**  
    //     - **Ensure all numeric operations (e.g., \$multiply) are applied to numeric fields only.**  
    //     - **Use `\$toDouble` or `\$toInt` when necessary** for fields like `price_per_sqm`, `available_units`, etc.  

    //     ✅ **Selecting Fields:**  
    //     - **Do NOT limit output fields**—return all fields unless explicitly specified by the query.  

    //     🚫 **Return Format Restrictions:**  
    //     - The output **must be a valid JSON array** containing the aggregation pipeline.
    //     - return {} if matching does not exist  
    //     - **No extra text, explanations, or formatting**—only return the JSON output.";
}
