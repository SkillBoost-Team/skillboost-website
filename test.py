from openai import OpenAI

base_url = "https://api.aimlapi.com/v1"

# Insert your AIML API key in the quotation marks instead of <YOUR_AIMLAPI_KEY>:
api_key = "d5c4611b53db4a718b67b056c3730ee5" 

system_prompt = "You are an expert in generating true/false questions for educational purposes."
user_prompt = "Create exactly three true/false technical questions based on the following course: Course Title: Développement Web Full Stack Course Description: Apprenez à créer des sites web interactifs avec HTML, CSS, JavaScript, Node.js et MongoD Output format (strictly follow this format): 1. [Question] - [True/False]  2. [Question] - [True/False]  3. [Question] - [True/False]"

api = OpenAI(api_key=api_key, base_url=base_url)


def main():
    completion = api.chat.completions.create(
        model="gpt-4o",
        messages=[
            {"role": "system", "content": system_prompt},
            {"role": "user", "content": user_prompt},
        ],
        temperature=0.7,
        max_tokens=10000,
    )

    response = completion.choices[0].message.content

    print("User:", user_prompt)
    print("AI:", response)


if __name__ == "__main__":
    main()
