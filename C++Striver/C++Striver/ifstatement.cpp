#include<stdio.h>

int main()
{
    int i,j;
    for(i=0;i<8;i++)
    {
        for(j=0;j<8;j++)
        {
            if((i+j)%2==0)
            printf("X");
            else
            printf("0");
        }
        printf("\n");
    }    
        return 0;
}
