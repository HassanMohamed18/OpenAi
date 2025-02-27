<?php

namespace App\Services;

use OpenAI;

class MongoServiceTest
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

        $translationPrompt = "Translate the following query into a clear and precise English question without changing its meaning:\n\n$query\n\nReturn only the translated question, nothing else.";

        $translationResponse = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a language expert. Translate user queries into precise English questions.'],
                ['role' => 'user', 'content' => $translationPrompt],
            ],
            'temperature' => 0.2,
        ]);

        $translatedQuestion = $translationResponse['choices'][0]['message']['content']; // Extract translated question


        $collectionName = 'realestate_test';
        $schema = 'available_units(numeric),project_name(string),price_per_sqm(numeric)';

        $prompt = "Given the following MongoDB schema for the '$collectionName' collection: $schema, generate a MongoDB aggregation pipeline in JSON format.
        
        ### **Instructions (Strictly Follow)**
        ✅ **Sort and/or filter only if necessary** based on the given query: \"$translatedQuestion\".  
        ✅ **If no sorting is needed, exclude the sort stage**.  
        ✅ **If no filtering is needed, exclude the match stage**. 
        ✅ **Limit the results to 5**. 
        ✅ **you can use multible filters**. 
        ✅ **Use only fields specified in the schema** ($schema).  
        ✅ **Respect data types** (e.g., numeric fields must have numeric values).  
        ✅ **Use regex for partial string matching**, but **do NOT** include `^` at the beginning or `$` at the end of the pattern.  
        ✅ **Convert date-related values into Unix timestamps (seconds).**  
        ✅ **Ignore conditions that attempt to filter a numeric field using a string.**
        ✅ **Do not select only the required fields** 
        ✅ **Exclude `null` values from the sorting process**.
       **Ensure that fields used in any mathematical operations (such as \"\$multiply\") are numeric**. Use \"\$toDouble\" or \"\$toInt\" for converting fields like `price_per_sqm` or `available_units` to numeric types before performing any mathematical operations.
          Return only a valid JSON array, nothing else.";
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

        //     $collectionName = 'realestate';
        // $schema = 'project_name(string),project_type(string) may be resedintional or commercial,total_units(numeric),available_units(numeric),launch_date(numeric),completion_date(numeric),project_size(string),area_name(string),region(string),dld_area_name(string),developer_name(string),table_name(string),starting_price_range(numeric),min_price_range_SQ(numeric),project_size_sqmt(numeric)';  

        // $prompt = "Given the following MongoDB schema for the '$collectionName' collection:  
        // **Schema:** $schema  

        // ### **Strict Query Generation Instructions**
        // Your task is to generate an accurate **MongoDB aggregation pipeline in JSON format** based on the given user query:  
        // ➡️ **User Query:** \"$translatedQuestion\"  

        // ### **Rules to Follow for Query Construction**
        // ✅ **Filtering & Sorting (Only When Needed):**  
        // - If the query requires filtering, include a **\$match** stage; otherwise, exclude it.  
        // - If sorting is required, include a **\$sort** stage. **Exclude it if unnecessary.**  
        // - Ensure sorting ignores `null` values.  

        // ✅ **Limit the Results:**  
        // - Always limit the output to **5 results**.  

        // ✅ **Filtering Logic:**  
        // - Use **multiple filters** when applicable.  
        // - **Respect Data Types** → Ensure numeric fields are compared numerically.  
        // - **Use Regex  for Partial String Matching**, but avoid `^` and `$` anchors to ensure flexible searches.  
        // - **Ignore queries that attempt to filter a numeric field using a string.**  

        // ✅ **Handling Dates:**  
        // - Convert **date-related values** (`launch_date`, `completion_date`) into **Unix timestamps (seconds)** when processing.  

        // ✅ **Data Integrity & Mathematical Operations:**  
        // - **Ensure all numeric operations (e.g., \$multiply) are applied to numeric fields only.**  
        // - **Use `\$toDouble` or `\$toInt` when necessary** for fields like `price_per_sqm`, `available_units`, etc.  

        // ✅ **Selecting Fields:**  
        // - **Do NOT limit output fields**—return all fields unless explicitly specified by the query.  

        // 🚫 **Return Format Restrictions:**  
        // - The output **must be a valid JSON array** containing the aggregation pipeline.  
        // - **No extra text, explanations, or formatting**—only return the JSON output.";

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
        return $generatedPipeline = $response->choices[0]->message->content;
    }
}
